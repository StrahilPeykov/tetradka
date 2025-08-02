#!/usr/bin/env node

const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs');
const glob = require('glob');
const chalk = require('chalk');
const minimist = require('minimist');

// Parse command line arguments
const args = minimist(process.argv.slice(2));

// Configuration
const config = {
    host: 'ftpupload.net',
    user: 'if0_39618140',
    password: 'tetradkatapass1',
    secure: false,
    
    // Paths for wp-content level repo
    themeLocalDir: './themes/tetradkata-theme/',
    themeRemoteDir: '/htdocs/wp-content/themes/tetradkata-theme/',
    
    // Deployment options from command line
    dryRun: args['dry-run'] || false,
    assetsOnly: args['assets-only'] || false,
    verbose: args.verbose || args.v || false
};

// Logging functions
const log = {
    info: (msg) => console.log(chalk.blue('ℹ'), msg),
    success: (msg) => console.log(chalk.green('✅'), msg),
    warning: (msg) => console.log(chalk.yellow('⚠'), msg),
    error: (msg) => console.log(chalk.red('❌'), msg),
    verbose: (msg) => config.verbose && console.log(chalk.gray('🔍'), msg),
    title: (msg) => console.log(chalk.bold.cyan('\n🚀', msg))
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
        'themes/tetradkata-theme/**/*.eot'
    ];
    
    let files = [];
    patterns.forEach(pattern => {
        const matches = glob.sync(pattern, { 
            ignore: [
                '**/node_modules/**',
                '**/.git/**',
                '**/*.map',
                '**/.DS_Store',
                '**/.gitkeep'
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
                '**/.DS_Store'
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
        process.stdout.write(`\r${chalk.blue('📤')} Progress: ${progress}% (${successCount}/${totalFiles})`);
    }
    
    console.log(''); // New line after progress
    log.success(`Theme upload completed: ${successCount}/${totalFiles} files`);
    return successCount === totalFiles;
}

// Main deployment function
async function deploy() {
    log.title('Tetradkata Theme Deployment');
    
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
        
        await client.access({
            host: config.host,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        log.success('Connected to FTP server');
        
        // Upload theme files
        const themeSuccess = await uploadTheme(client);
        
        if (themeSuccess) {
            log.success('🎉 Deployment completed successfully!');
            log.info(`Visit your site: ${chalk.underline('https://tetradkata.rf.gd')}`);
        } else {
            log.warning('Deployment completed with some errors');
        }
        
    } catch (error) {
        log.error(`Deployment failed: ${error.message}`);
        if (config.verbose) {
            console.error(error);
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
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        log.success('FTP connection successful!');
        
        // Test remote directory access
        await client.cd('/htdocs/wp-content/themes/');
        log.success('Remote theme directory accessible');
        
    } catch (error) {
        log.error(`Connection failed: ${error.message}`);
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

${chalk.bold('Manual Usage:')}
  node deploy.js                    Deploy all files
  node deploy.js --assets-only      Deploy assets only
  node deploy.js --dry-run          Preview deployment
  node deploy.js --verbose          Detailed output

${chalk.bold('Examples:')}
  npm run deploy:dry-run            Preview deployment
  npm run deploy:assets             Deploy only CSS/JS changes
  npm run deploy:verbose            Deploy with detailed logs
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