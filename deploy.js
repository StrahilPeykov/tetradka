#!/usr/bin/env node

const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs');
const glob = require('glob');
const chalk = require('chalk');
const minimist = require('minimist');

// Parse command line arguments
const args = minimist(process.argv.slice(2));

// Environment detection and configuration
const environments = {
    local: {
        host: process.env.FTP_HOST_LOCAL || 'localhost',
        secure: false,
        description: 'Local Development',
        wp_environment: 'local'
    },
    staging: {
        host: process.env.FTP_HOST_STAGING || process.env.FTP_HOST,
        secure: process.env.FTP_SECURE === 'true' || true,
        description: 'Staging Environment',
        wp_environment: 'staging'
    },
    production: {
        host: process.env.FTP_HOST_PRODUCTION || process.env.FTP_HOST,
        secure: process.env.FTP_SECURE === 'true' || true,
        description: 'Production Environment',
        wp_environment: 'production'
    }
};

// Detect environment from arguments or auto-detect
const targetEnvironment = args.env || args.environment || detectEnvironmentFromHost();

function detectEnvironmentFromHost() {
    const host = process.env.FTP_HOST || '';
    
    if (host.includes('staging') || host.includes('dev.') || host.includes('test.')) {
        return 'staging';
    } else if (host.includes('localhost') || host.includes('127.0.0.1') || host.includes('.local')) {
        return 'local';
    } else {
        return 'production';
    }
}

// Validate environment variables
function validateEnvironment() {
    const env = environments[targetEnvironment];
    
    if (!env) {
        console.error(chalk.red('❌ Invalid environment:'), targetEnvironment);
        console.error(chalk.yellow('   Valid environments: local, staging, production'));
        process.exit(1);
    }

    const requiredVars = ['FTP_HOST', 'FTP_USER', 'FTP_PASSWORD'];
    const missingVars = [];

    requiredVars.forEach(varName => {
        const envSpecificVar = `${varName}_${targetEnvironment.toUpperCase()}`;
        if (!process.env[envSpecificVar] && !process.env[varName]) {
            missingVars.push(varName);
        }
    });

    if (missingVars.length > 0) {
        console.error(chalk.red('❌ Missing required environment variables:'));
        missingVars.forEach(varName => {
            console.error(chalk.red(`   - ${varName} or ${varName}_${targetEnvironment.toUpperCase()}`));
        });
        console.error(chalk.yellow('\n💡 Set environment variables:'));
        console.error(chalk.yellow('   export FTP_HOST=your-host'));
        console.error(chalk.yellow('   export FTP_USER=your-username'));  
        console.error(chalk.yellow('   export FTP_PASSWORD=your-password'));
        console.error(chalk.yellow('\n   Or environment-specific:'));
        console.error(chalk.yellow(`   export FTP_HOST_${targetEnvironment.toUpperCase()}=your-staging-host`));
        process.exit(1);
    }

    return env;
}

// Get environment-specific configuration
function getEnvironmentConfig() {
    const env = environments[targetEnvironment];
    const envUpper = targetEnvironment.toUpperCase();

    return {
        host: process.env[`FTP_HOST_${envUpper}`] || process.env.FTP_HOST,
        user: process.env[`FTP_USER_${envUpper}`] || process.env.FTP_USER,
        password: process.env[`FTP_PASSWORD_${envUpper}`] || process.env.FTP_PASSWORD,
        secure: process.env[`FTP_SECURE_${envUpper}`] === 'true' || env.secure,
        port: parseInt(process.env[`FTP_PORT_${envUpper}`] || process.env.FTP_PORT || '21'),
        
        // Paths
        themeLocalDir: './themes/tetradkata-theme/',
        themeRemoteDir: process.env[`FTP_REMOTE_DIR_${envUpper}`] || process.env.FTP_REMOTE_DIR || '/htdocs/wp-content/themes/tetradkata-theme/',
        
        // Deployment options
        dryRun: args['dry-run'] || false,
        assetsOnly: args['assets-only'] || false,
        verbose: args.verbose || args.v || false,
        
        // Environment info
        environment: targetEnvironment,
        description: env.description,
        wp_environment: env.wp_environment,
        
        // CI/CD detection
        isCI: process.env.CI || process.env.GITHUB_ACTIONS || false
    };
}

const config = getEnvironmentConfig();

// Logging functions with environment context
const log = {
    info: (msg) => console.log(chalk.blue('ℹ'), msg),
    success: (msg) => console.log(chalk.green('✅'), msg),
    warning: (msg) => console.log(chalk.yellow('⚠'), msg),
    error: (msg) => console.log(chalk.red('❌'), msg),
    verbose: (msg) => config.verbose && console.log(chalk.gray('🔍'), msg),
    title: (msg) => console.log(chalk.bold.cyan('\n🚀', msg)),
    ci: (msg) => config.isCI && console.log(chalk.magenta('🤖'), msg),
    env: (msg) => console.log(chalk.bold.magenta('🌍'), msg)
};

// Environment-specific file filtering
function getEnvironmentSpecificFiles() {
    const basePatterns = [
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

    const ignorePatterns = [
        '**/node_modules/**',
        '**/.git/**',
        '**/*.map',
        '**/.DS_Store',
        '**/.gitkeep',
        '**/test-*.js',
        '**/.env*',
        '**/readme.txt',
        '**/README.md'
    ];

    // Environment-specific exclusions
    if (config.environment === 'production') {
        ignorePatterns.push(
            '**/debug-*.php',
            '**/test-*.php',
            '**/*.dev.js',
            '**/*.debug.css'
        );
    }

    let files = [];
    basePatterns.forEach(pattern => {
        const matches = glob.sync(pattern, { 
            ignore: ignorePatterns
        });
        files = files.concat(matches);
    });

    return [...new Set(files)].map(file => file.replace(/\\/g, '/'));
}

// Get only asset files (for assets-only deployment)
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
    
    return [...new Set(files)].map(file => file.replace(/\\/g, '/'));
}

// Enhanced file upload with retry logic
async function uploadFile(client, localPath, remotePath, retries = 3) {
    const normalizedRemotePath = remotePath.replace(/\\/g, '/');
    const remoteDir = path.dirname(normalizedRemotePath).replace(/\\/g, '/');
    
    try {
        await client.ensureDir(remoteDir);
        
        if (config.dryRun) {
            log.verbose(`[DRY RUN] Would upload: ${localPath} → ${normalizedRemotePath}`);
            return true;
        }
        
        // Check if file exists and compare modification time
        let shouldUpload = true;
        if (config.environment === 'production') {
            try {
                const remoteFileInfo = await client.list(normalizedRemotePath);
                const localStats = fs.statSync(localPath);
                
                if (remoteFileInfo.length > 0) {
                    const remoteFile = remoteFileInfo[0];
                    if (localStats.mtime <= remoteFile.modifiedAt) {
                        log.verbose(`Skipping unchanged file: ${path.basename(localPath)}`);
                        shouldUpload = false;
                    }
                }
            } catch (e) {
                // File doesn't exist remotely, upload it
            }
        }
        
        if (shouldUpload) {
            await client.uploadFrom(localPath, normalizedRemotePath);
            log.verbose(`Uploaded: ${path.basename(localPath)}`);
        }
        
        return true;
    } catch (error) {
        if (retries > 0) {
            log.warning(`Retry upload for ${localPath} (${retries} attempts left)`);
            await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second
            return uploadFile(client, localPath, remotePath, retries - 1);
        }
        
        log.error(`Failed to upload ${localPath}: ${error.message}`);
        return false;
    }
}

// Pre-deployment validation
function validateLocalEnvironment() {
    log.title('Validating Local Environment');
    
    // Check if theme directory exists
    if (!fs.existsSync(config.themeLocalDir)) {
        log.error(`Theme directory not found: ${config.themeLocalDir}`);
        return false;
    }

    // Check required files
    const requiredFiles = [
        'themes/tetradkata-theme/style.css',
        'themes/tetradkata-theme/functions.php',
        'themes/tetradkata-theme/index.php'
    ];

    for (const file of requiredFiles) {
        if (!fs.existsSync(file)) {
            log.error(`Required file missing: ${file}`);
            return false;
        }
    }

    // Check if inc/environment.php exists (new requirement)
    if (!fs.existsSync('themes/tetradkata-theme/inc/environment.php')) {
        log.error('Environment management file missing: themes/tetradkata-theme/inc/environment.php');
        log.error('Please create this file first before deploying.');
        return false;
    }

    log.success('Local environment validation passed');
    return true;
}

// Upload theme files with progress tracking
async function uploadTheme(client) {
    log.title(`Uploading Theme Files to ${config.description}`);
    
    const files = config.assetsOnly ? getAssetFiles() : getEnvironmentSpecificFiles();
    let successCount = 0;
    let totalFiles = files.length;
    
    log.info(`Found ${totalFiles} files to upload`);
    log.env(`Target Environment: ${config.environment.toUpperCase()}`);
    
    if (totalFiles === 0) {
        log.warning('No files found to upload');
        return false;
    }
    
    const startTime = Date.now();
    
    for (let i = 0; i < files.length; i++) {
        const localFile = files[i];
        const relativePath = localFile.replace('themes/tetradkata-theme/', '').replace(/\\/g, '/');
        const remotePath = path.posix.join(config.themeRemoteDir, relativePath);
        
        const success = await uploadFile(client, localFile, remotePath);
        if (success) successCount++;
        
        // Progress indicator
        const progress = Math.round(((i + 1) / totalFiles) * 100);
        
        if (config.isCI) {
            if ((i + 1) % 10 === 0 || (i + 1) === totalFiles) {
                log.ci(`Progress: ${progress}% (${i + 1}/${totalFiles})`);
            }
        } else {
            process.stdout.write(`\r${chalk.blue('📤')} Progress: ${progress}% (${i + 1}/${totalFiles})`);
        }
    }
    
    if (!config.isCI) {
        console.log(''); // New line after progress
    }
    
    const endTime = Date.now();
    const duration = ((endTime - startTime) / 1000).toFixed(2);
    
    log.success(`Theme upload completed: ${successCount}/${totalFiles} files in ${duration}s`);
    
    if (successCount < totalFiles) {
        log.warning(`${totalFiles - successCount} files failed to upload`);
    }
    
    return successCount === totalFiles;
}

// Create wp-config.php environment constant
async function setWordPressEnvironment(client) {
    if (config.dryRun) {
        log.verbose('[DRY RUN] Would set WordPress environment constant');
        return;
    }

    try {
        log.info('Setting WordPress environment constant...');
        
        // Create a small PHP file to set the environment
        const envFileContent = `<?php
// Auto-generated environment configuration
if (!defined('WP_ENVIRONMENT_TYPE')) {
    define('WP_ENVIRONMENT_TYPE', '${config.wp_environment}');
}
`;
        
        const tempFile = './temp-env.php';
        fs.writeFileSync(tempFile, envFileContent);
        
        // Upload to wp-content directory
        const remotePath = config.themeRemoteDir.replace('/themes/tetradkata-theme/', '/tetradkata-env.php');
        await uploadFile(client, tempFile, remotePath);
        
        // Clean up temp file
        fs.unlinkSync(tempFile);
        
        log.success('WordPress environment configured');
    } catch (error) {
        log.warning(`Failed to set WordPress environment: ${error.message}`);
    }
}

// Post-deployment validation
async function validateDeployment(client) {
    log.title('Validating Deployment');
    
    try {
        // Check if key files exist on remote
        const keyFiles = [
            'style.css',
            'functions.php',
            'inc/environment.php'
        ];
        
        let validationPassed = true;
        
        for (const file of keyFiles) {
            const remotePath = path.posix.join(config.themeRemoteDir, file);
            try {
                const fileList = await client.list(path.dirname(remotePath));
                const fileName = path.basename(remotePath);
                const fileExists = fileList.some(item => item.name === fileName);
                
                if (fileExists) {
                    log.verbose(`✓ ${file} exists on remote`);
                } else {
                    log.error(`✗ ${file} missing on remote`);
                    validationPassed = false;
                }
            } catch (error) {
                log.error(`Failed to check ${file}: ${error.message}`);
                validationPassed = false;
            }
        }
        
        if (validationPassed) {
            log.success('Deployment validation passed');
        } else {
            log.error('Deployment validation failed');
        }
        
        return validationPassed;
    } catch (error) {
        log.error(`Validation error: ${error.message}`);
        return false;
    }
}

// Main deployment function
async function deploy() {
    const env = validateEnvironment();
    
    if (config.isCI) {
        log.title(`Tetradkata Theme Deployment (CI/CD) - ${env.description}`);
        log.ci(`Branch: ${process.env.GITHUB_REF || 'Unknown'}`);
        log.ci(`Commit: ${process.env.GITHUB_SHA?.substring(0, 7) || 'Unknown'}`);
        log.ci(`Environment: ${config.environment}`);
    } else {
        log.title(`Tetradkata Theme Deployment - ${env.description}`);
    }
    
    if (config.dryRun) {
        log.warning('DRY RUN MODE - No files will actually be uploaded');
    }
    
    if (config.assetsOnly) {
        log.info('ASSETS ONLY MODE - Uploading CSS, JS, and images only');
    }
    
    // Pre-deployment validation
    if (!validateLocalEnvironment()) {
        process.exit(1);
    }
    
    const client = new ftp.Client();
    client.ftp.verbose = config.verbose;
    
    try {
        log.info('Connecting to FTP server...');
        log.verbose(`Host: ${config.host}:${config.port}`);
        log.verbose(`User: ${config.user}`);
        log.verbose(`Secure: ${config.secure}`);
        log.env(`Environment: ${config.environment}`);
        
        await client.access({
            host: config.host,
            port: config.port,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        log.success('Connected to FTP server');
        
        // Set WordPress environment
        await setWordPressEnvironment(client);
        
        // Upload theme files
        const uploadSuccess = await uploadTheme(client);
        
        // Validate deployment
        const validationSuccess = await validateDeployment(client);
        
        if (uploadSuccess && validationSuccess) {
            log.success('🎉 Deployment completed successfully!');
            log.env(`Environment: ${config.environment.toUpperCase()}`);
            
            // Environment-specific success messages
            if (config.environment === 'production') {
                log.info('🌟 Production deployment complete! Your site is now live.');
                log.info('📊 Remember to:');
                log.info('   - Set up analytics tracking');
                log.info('   - Configure payment gateway credentials');
                log.info('   - Test all functionality');
            } else if (config.environment === 'staging') {
                log.info('🧪 Staging deployment complete! Ready for testing.');
                log.info('🔍 Test thoroughly before deploying to production.');
            }
            
            if (config.isCI) {
                log.ci('Automatic deployment completed successfully');
                console.log('::notice::Deployment completed successfully');
            }
        } else {
            log.warning('Deployment completed with issues');
            if (config.isCI) {
                process.exit(1);
            }
        }
        
    } catch (error) {
        log.error(`Deployment failed: ${error.message}`);
        
        if (config.verbose) {
            console.error(error);
        }
        
        // Environment-specific error handling
        if (error.message.includes('530')) {
            log.error('Authentication failed. Check your FTP credentials.');
        } else if (error.message.includes('ENOTFOUND')) {
            log.error('Host not found. Check your FTP hostname.');
        } else if (error.message.includes('timeout')) {
            log.error('Connection timeout. Check network connectivity.');
        }
        
        if (config.isCI) {
            console.log('::error::Deployment failed');
        }
        
        process.exit(1);
    } finally {
        client.close();
    }
}

// Test FTP connection with environment support
async function testConnection() {
    const env = validateEnvironment();
    
    log.title(`Testing FTP Connection - ${env.description}`);
    log.env(`Target Environment: ${config.environment}`);
    
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
        log.info(`Connected to: ${config.host} (${config.environment})`);
        
        // Test remote directory access
        try {
            await client.cd(path.dirname(config.themeRemoteDir));
            log.success('Remote theme directory accessible');
            
            // List contents
            const list = await client.list();
            log.info(`Found ${list.length} items in remote directory`);
            
            if (config.verbose && list.length > 0) {
                log.verbose('Remote directory contents:');
                list.slice(0, 5).forEach(item => {
                    const type = item.type === 'd' ? '[DIR]' : '[FILE]';
                    log.verbose(`  ${type} ${item.name}`);
                });
                if (list.length > 5) {
                    log.verbose(`  ... and ${list.length - 5} more items`);
                }
            }
        } catch (error) {
            log.warning('Remote directory access test failed');
            log.verbose(`Directory error: ${error.message}`);
        }
        
        if (config.isCI) {
            log.ci('FTP connection test passed in CI environment');
        }
        
        log.success(`✅ FTP connection test successful for ${config.environment} environment!`);
        
    } catch (error) {
        log.error(`Connection failed: ${error.message}`);
        
        // Provide helpful error messages
        if (error.message.includes('530')) {
            log.error('Authentication failed - check credentials for ' + config.environment);
        } else if (error.message.includes('ENOTFOUND')) {
            log.error('Host not found - check hostname for ' + config.environment);
        }
        
        if (config.isCI) {
            console.log('::error::FTP connection test failed');
            process.exit(1);
        }
    } finally {
        client.close();
    }
}

// Enhanced help with environment info
function showHelp() {
    console.log(`
${chalk.bold.cyan('Tetradkata Theme Deployment Tool v2.0')}

${chalk.bold('Usage:')}
  npm run deploy                           Deploy to auto-detected environment
  npm run deploy -- --env production      Deploy to specific environment
  npm run deploy -- --env staging --dry-run     Preview staging deployment
  npm run test-connection                  Test FTP connection

${chalk.bold('Environments:')}
  ${chalk.green('local')}       - Local development (usually localhost)
  ${chalk.yellow('staging')}     - Staging environment (for testing)  
  ${chalk.red('production')}   - Live production environment

${chalk.bold('Environment Variables:')}
  ${chalk.cyan('General:')}
  FTP_HOST, FTP_USER, FTP_PASSWORD         Default credentials
  FTP_PORT, FTP_SECURE, FTP_REMOTE_DIR     Optional settings
  
  ${chalk.cyan('Environment-specific (override general):')}
  FTP_HOST_STAGING, FTP_USER_STAGING       Staging-specific credentials
  FTP_HOST_PRODUCTION, FTP_USER_PRODUCTION Production-specific credentials

${chalk.bold('Options:')}
  --env <environment>          Target environment (local/staging/production)
  --dry-run                    Preview what would be uploaded
  --assets-only                Deploy only CSS/JS/images
  --verbose, -v                Detailed output
  --help, -h                   Show this help

${chalk.bold('Examples:')}
  ${chalk.gray('# Deploy to production')}
  FTP_HOST=ftp.example.com FTP_USER=user FTP_PASSWORD=pass npm run deploy -- --env production
  
  ${chalk.gray('# Preview staging deployment')}
  npm run deploy -- --env staging --dry-run
  
  ${chalk.gray('# Deploy only assets to current environment')}
  npm run deploy -- --assets-only

${chalk.bold('Environment Detection:')}
  - Checks --env argument first
  - Auto-detects from hostname patterns:
    * Contains 'staging', 'dev.', 'test.' → staging
    * Contains 'localhost', '127.0.0.1', '.local' → local  
    * Everything else → production

${chalk.bold('Security Notes:')}
  - Never commit credentials to version control
  - Use environment variables or .env files (git-ignored)
  - Different credentials for each environment recommended
  - Production deployments require extra validation
`);
}

// CLI entry point with enhanced argument parsing
if (require.main === module) {
    if (args.help || args.h) {
        showHelp();
    } else if (args['test-connection']) {
        testConnection();
    } else {
        console.log(chalk.bold.blue(`🚀 Starting deployment to ${targetEnvironment.toUpperCase()} environment...\n`));
        deploy();
    }
}

module.exports = { deploy, testConnection, environments };