#!/usr/bin/env node

const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs');
const glob = require('glob');
const chalk = require('chalk');
const minimist = require('minimist');

// Parse command line arguments
const args = minimist(process.argv.slice(2));

// Validate environment variables
if (!process.env.FTP_HOST || !process.env.FTP_USER || !process.env.FTP_PASSWORD) {
    console.error(chalk.red('❌ Missing required environment variables:'));
    if (!process.env.FTP_HOST) console.error(chalk.red('   - FTP_HOST'));
    if (!process.env.FTP_USER) console.error(chalk.red('   - FTP_USER'));
    if (!process.env.FTP_PASSWORD) console.error(chalk.red('   - FTP_PASSWORD'));
    console.error(chalk.yellow('\n💡 Set environment variables before running:'));
    console.error(chalk.yellow('   export FTP_HOST=your-host'));
    console.error(chalk.yellow('   export FTP_USER=your-username'));
    console.error(chalk.yellow('   export FTP_PASSWORD=your-password'));
    console.error(chalk.yellow('   npm run deploy\n'));
    process.exit(1);
}

// Configuration - uses environment variables only
const config = {
    host: process.env.FTP_HOST,
    user: process.env.FTP_USER,
    password: process.env.FTP_PASSWORD,
    secure: process.env.FTP_SECURE === 'true' || false,
    port: process.env.FTP_PORT ? parseInt(process.env.FTP_PORT) : 21,
    
    // Paths for wp-content level repo
    themeLocalDir: './themes/tetradkata-theme/',
    themeRemoteDir: process.env.FTP_REMOTE_DIR || '/htdocs/wp-content/themes/tetradkata-theme/',
    
    // Deployment options from command line
    dryRun: args['dry-run'] || false,
    assetsOnly: args['assets-only'] || false,
    verbose: args.verbose || args.v || false,
    
    // CI/CD detection
    isCI: process.env.CI || process.env.GITHUB_ACTIONS || false
};

// Logging functions
const log = {
    info: (msg) => console.log(chalk.blue('ℹ'), msg),
    success: (msg) => console.log(chalk.green('✅'), msg),
    warning: (msg) => console.log(chalk.yellow('⚠'), msg),
    error: (msg) => console.log(chalk.red('❌'), msg),
    verbose: (msg) => config.verbose && console.log(chalk.gray('🔍'), msg),
    title: (msg) => console.log(chalk.bold.cyan('\n🚀', msg)),
    ci: (msg) => config.isCI && console.log(chalk.magenta('🤖'), msg)
};

// Check if theme directory exists
function validateLocalDirs() {
    if (!fs.existsSync(config.themeLocalDir)) {
        log.error(`Theme directory not found: ${config.themeLocalDir}`);
        return false;
    }
    
    log.success('Theme directory found');
    return true;
}

// Get files to upload for theme
function getThemeFiles() {
    const patterns = [
        'themes/tetradkata-theme/**/*.php',
        'themes/tetradkata-theme/**/*.css',
        'themes/tetradkata-theme/**/*.js',
        'themes/tetradkata-theme/**/*.json',
        'themes/tetradkata-theme/**/*.png',
        'themes/tetradkata-theme/**/*.jpg',
        'themes/tetradkata-theme/**/*.jpeg',
        'themes/tetradkata-theme/**/*.gif',
        'themes/tetradkata-theme/**/*.svg',
        'themes/tetradkata-theme/**/*.woff',
        'themes/tetradkata-theme/**/*.woff2',
        'themes/tetradkata-theme/**/*.ttf',
        'themes/tetradkata-theme/**/*.eot',
        'themes/tetradkata-theme/**/*.mo',
        'themes/tetradkata-theme/**/*.po',
        'themes/tetradkata-theme/**/*.pot'
    ];
    
    let files = [];
    patterns.forEach(pattern => {
        const matches = glob.sync(pattern, { 
            ignore: [
                '**/node_modules/**',
                '**/.git/**',
                '**/*.map',
                '**/.DS_Store',
                '**/.gitkeep',
                '**/test-*.js',  // Exclude test files
                '**/.env*'       // Exclude environment files
            ]
        });
        files = files.concat(matches);
    });
    
    // Normalize paths to use forward slashes for consistency
    return [...new Set(files)].map(file => file.replace(/\\/g, '/'));
}

// Get only asset files (CSS, JS, images)
function getAssetFiles() {
    const patterns = [
        'themes/tetradkata-theme/assets/**/*',
        'themes/tetradkata-theme/style.css'
    ];
    
    let files = [];
    patterns.forEach(pattern => {
        const matches = glob.sync(pattern, { 
            ignore: [
                '**/node_modules/**',
                '**/.git/**',
                '**/*.map',
                '**/.DS_Store',
                '**/test-*.js'
            ]
        });
        files = files.concat(matches);
    });
    
    // Normalize paths to use forward slashes for consistency
    return [...new Set(files)].map(file => file.replace(/\\/g, '/'));
}

// Upload a single file
async function uploadFile(client, localPath, remotePath) {
    try {
        // Ensure remote path uses forward slashes for FTP
        const normalizedRemotePath = remotePath.replace(/\\/g, '/');
        const remoteDir = path.dirname(normalizedRemotePath).replace(/\\/g, '/');
        
        await client.ensureDir(remoteDir);
        
        if (config.dryRun) {
            log.verbose(`[DRY RUN] Would upload: ${localPath} → ${normalizedRemotePath}`);
            return true;
        }
        
        await client.uploadFrom(localPath, normalizedRemotePath);
        log.verbose(`Uploaded: ${path.basename(localPath)}`);
        return true;
    } catch (error) {
        log.error(`Failed to upload ${localPath}: ${error.message}`);
        return false;
    }
}

// Upload theme files
async function uploadTheme(client) {
    log.title('Uploading Theme Files');
    
    const files = config.assetsOnly ? getAssetFiles() : getThemeFiles();
    let successCount = 0;
    let totalFiles = files.length;
    
    log.info(`Found ${totalFiles} files to upload`);
    
    if (totalFiles === 0) {
        log.warning('No files found to upload');
        return false;
    }
    
    for (const localFile of files) {
        // Convert local path to remote path - fix Windows path separators
        const relativePath = localFile.replace('themes/tetradkata-theme/', '').replace(/\\/g, '/');
        const remotePath = path.posix.join(config.themeRemoteDir, relativePath);
        
        const success = await uploadFile(client, localFile, remotePath);
        if (success) successCount++;
        
        // Progress indicator
        const progress = Math.round((successCount / totalFiles) * 100);
        
        if (config.isCI) {
            // In CI, show less frequent progress updates
            if (successCount % 5 === 0 || successCount === totalFiles) {
                log.ci(`Progress: ${progress}% (${successCount}/${totalFiles})`);
            }
        } else {
            process.stdout.write(`\r${chalk.blue('📤')} Progress: ${progress}% (${successCount}/${totalFiles})`);
        }
    }
    
    if (!config.isCI) {
        console.log(''); // New line after progress
    }
    
    log.success(`Theme upload completed: ${successCount}/${totalFiles} files`);
    return successCount === totalFiles;
}

// Main deployment function
async function deploy() {
    if (config.isCI) {
        log.title('Tetradkata Theme Deployment (CI/CD)');
        log.ci(`Deploying from: ${process.env.GITHUB_REF || 'Unknown branch'}`);
        log.ci(`Commit: ${process.env.GITHUB_SHA?.substring(0, 7) || 'Unknown'}`);
    } else {
        log.title('Tetradkata Theme Deployment');
    }
    
    if (config.dryRun) {
        log.warning('DRY RUN MODE - No files will actually be uploaded');
    }
    
    if (config.assetsOnly) {
        log.info('ASSETS ONLY MODE - Uploading CSS, JS, and images only');
    }
    
    // Validate local directories
    if (!validateLocalDirs()) {
        log.error('Local directories validation failed');
        process.exit(1);
    }
    
    const client = new ftp.Client();
    client.ftp.verbose = config.verbose;
    
    try {
        log.info('Connecting to FTP server...');
        log.verbose(`Host: ${config.host}:${config.port}`);
        log.verbose(`User: ${config.user}`);
        log.verbose(`Secure: ${config.secure}`);
        
        await client.access({
            host: config.host,
            port: config.port,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        log.success('Connected to FTP server');
        
        // Upload theme files
        const themeSuccess = await uploadTheme(client);
        
        if (themeSuccess) {
            log.success('🎉 Deployment completed successfully!');
            log.info(`Visit your site: ${chalk.underline(process.env.SITE_URL || 'https://your-site.com')}`);
            
            if (config.isCI) {
                log.ci('Automatic deployment completed');
                // GitHub Actions specific output
                console.log('::notice::Deployment completed successfully');
            }
        } else {
            log.warning('Deployment completed with some errors');
            if (config.isCI) {
                process.exit(1); // Fail the CI build
            }
        }
        
    } catch (error) {
        log.error(`Deployment failed: ${error.message}`);
        if (config.verbose) {
            console.error(error);
        }
        
        if (config.isCI) {
            console.log('::error::Deployment failed');
        }
        
        process.exit(1);
    } finally {
        client.close();
    }
}

// Test FTP connection
async function testConnection() {
    log.title('Testing FTP Connection');
    
    const client = new ftp.Client();
    
    try {
        await client.access({
            host: config.host,
            port: config.port,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        log.success('FTP connection successful!');
        log.info(`Connected to: ${config.host}`);
        
        // Test remote directory access
        await client.cd(path.dirname(config.themeRemoteDir));
        log.success('Remote theme directory accessible');
        
        if (config.isCI) {
            log.ci('FTP connection test passed in CI environment');
        }
        
    } catch (error) {
        log.error(`Connection failed: ${error.message}`);
        if (config.isCI) {
            console.log('::error::FTP connection test failed');
            process.exit(1);
        }
    } finally {
        client.close();
    }
}

// Show help
function showHelp() {
    console.log(`
${chalk.bold.cyan('Tetradkata Theme Deployment Tool')}

${chalk.bold('Usage:')}
  npm run deploy                    Deploy all theme files
  npm run deploy:assets             Deploy CSS/JS/images only  
  npm run deploy:dry-run            Preview what would be uploaded
  npm run deploy:verbose            Deploy with detailed output
  npm run test-connection           Test FTP connection

${chalk.bold('Environment Variables (Required):')}
  FTP_HOST                          FTP server hostname
  FTP_USER                          FTP username
  FTP_PASSWORD                      FTP password
  
${chalk.bold('Environment Variables (Optional):')}
  FTP_PORT                          FTP port (default: 21)
  FTP_SECURE                        Use FTPS (default: false)
  FTP_REMOTE_DIR                    Remote directory path
  SITE_URL                          Your website URL

${chalk.bold('Examples:')}
  FTP_HOST=ftp.example.com FTP_USER=user FTP_PASSWORD=pass npm run deploy
  npm run deploy:dry-run            Preview deployment
  npm run deploy:assets             Deploy only CSS/JS changes
  
${chalk.bold('Security Note:')}
  Never commit credentials to version control!
  Use environment variables or .env files (git-ignored)
`);
}

// CLI entry point
if (require.main === module) {
    if (args.help || args.h) {
        showHelp();
    } else if (args['test-connection']) {
        testConnection();
    } else {
        deploy();
    }
}

module.exports = { deploy, testConnection };