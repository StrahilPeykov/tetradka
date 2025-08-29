#!/usr/bin/env node

const ftp = require('basic-ftp');
const chalk = require('chalk');

// Check for required environment variables
if (!process.env.FTP_HOST || !process.env.FTP_USER || !process.env.FTP_PASSWORD) {
    console.log(chalk.red('\n❌ Missing required environment variables!\n'));
    console.log(chalk.yellow('Please set the following environment variables:'));
    console.log(chalk.cyan('  FTP_HOST     - Your FTP server hostname'));
    console.log(chalk.cyan('  FTP_USER     - Your FTP username'));
    console.log(chalk.cyan('  FTP_PASSWORD - Your FTP password'));
    console.log(chalk.yellow('\nExample:'));
    console.log(chalk.gray('  export FTP_HOST=ftpupload.net'));
    console.log(chalk.gray('  export FTP_USER=your_username'));
    console.log(chalk.gray('  export FTP_PASSWORD=your_password'));
    console.log(chalk.gray('  npm run test-connection'));
    console.log(chalk.yellow('\nOr create a .env file (make sure it\'s in .gitignore):'));
    console.log(chalk.gray('  FTP_HOST=ftpupload.net'));
    console.log(chalk.gray('  FTP_USER=your_username'));
    console.log(chalk.gray('  FTP_PASSWORD=your_password\n'));
    process.exit(1);
}

// Configuration from environment variables only
const config = {
    host: process.env.FTP_HOST,
    user: process.env.FTP_USER,
    password: process.env.FTP_PASSWORD,
    secure: process.env.FTP_SECURE === 'true' || false,
    port: process.env.FTP_PORT ? parseInt(process.env.FTP_PORT) : 21
};

async function testFTPConnection() {
    console.log(chalk.bold.cyan('\n🔌 Testing FTP Connection\n'));
    
    const client = new ftp.Client();
    client.ftp.verbose = false;
    
    try {
        console.log(chalk.blue('ℹ'), 'Connecting to FTP server...');
        console.log(chalk.gray('  Host:'), config.host);
        console.log(chalk.gray('  Port:'), config.port);
        console.log(chalk.gray('  User:'), config.user);
        console.log(chalk.gray('  Secure:'), config.secure ? 'Yes' : 'No');
        
        await client.access({
            host: config.host,
            port: config.port,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        console.log(chalk.green('✅'), 'FTP connection successful!');
        
        // Test directory navigation
        console.log(chalk.blue('ℹ'), 'Testing directory access...');
        
        try {
            await client.cd('/htdocs/');
            console.log(chalk.green('✅'), 'htdocs directory accessible');
        } catch (error) {
            console.log(chalk.yellow('⚠'), 'htdocs directory not found - checking alternative paths...');
            try {
                await client.cd('/public_html/');
                console.log(chalk.green('✅'), 'public_html directory found');
            } catch (err) {
                console.log(chalk.yellow('⚠'), 'Standard directories not found - listing root directory...');
                const list = await client.list('/');
                console.log(chalk.blue('ℹ'), 'Available directories:');
                list.forEach(item => {
                    if (item.type === 'd') {
                        console.log(chalk.gray('    -'), item.name);
                    }
                });
            }
        }
        
        // Try to find WordPress installation
        try {
            await client.cd('/htdocs/wp-content/');
            console.log(chalk.green('✅'), 'WordPress wp-content directory found');
            
            await client.cd('themes/');
            console.log(chalk.green('✅'), 'themes directory accessible');
            
            // Check if our theme directory exists
            try {
                await client.cd('tetradkata-theme/');
                console.log(chalk.green('✅'), 'tetradkata-theme directory exists');
            } catch (error) {
                console.log(chalk.yellow('⚠'), 'tetradkata-theme directory not found - will be created during deployment');
            }
        } catch (error) {
            console.log(chalk.yellow('⚠'), 'WordPress installation not found in expected location');
            console.log(chalk.yellow('💡'), 'You may need to adjust the remote directory path in your deployment configuration');
        }
        
        // Get current working directory
        const pwd = await client.pwd();
        console.log(chalk.blue('ℹ'), `Current remote directory: ${pwd}`);
        
        // List some files to verify access
        console.log(chalk.blue('ℹ'), 'Listing remote files...');
        const list = await client.list();
        console.log(chalk.green('✅'), `Found ${list.length} items in current directory`);
        
        // Show first few items as example
        if (list.length > 0) {
            console.log(chalk.gray('  Sample items:'));
            list.slice(0, 5).forEach(item => {
                const type = item.type === 'd' ? '[DIR]' : '[FILE]';
                console.log(chalk.gray(`    ${type} ${item.name}`));
            });
            if (list.length > 5) {
                console.log(chalk.gray(`    ... and ${list.length - 5} more items`));
            }
        }
        
        console.log(chalk.bold.green('\n🎉 All FTP tests passed! Ready for deployment.\n'));
        
        // Show deployment command reminder
        console.log(chalk.cyan('📝 To deploy your theme, run:'));
        console.log(chalk.gray('   npm run deploy'));
        console.log(chalk.cyan('\n📝 For a dry run (preview without uploading):'));
        console.log(chalk.gray('   npm run deploy:dry-run\n'));
        
    } catch (error) {
        console.log(chalk.red('❌'), 'FTP test failed:');
        console.log(chalk.red('   '), error.message);
        
        // Provide helpful error messages
        if (error.message.includes('530')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), '• Check your FTP username and password');
            console.log(chalk.yellow('   '), '• Make sure your hosting account is active');
            console.log(chalk.yellow('   '), '• Verify the credentials with your hosting provider');
        }
        
        if (error.message.includes('ENOTFOUND') || error.message.includes('getaddrinfo')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), '• Check the FTP hostname is correct');
            console.log(chalk.yellow('   '), '• Make sure you have internet connection');
            console.log(chalk.yellow('   '), '• Verify the hostname with your hosting provider');
        }
        
        if (error.message.includes('timeout')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), '• Check your internet connection');
            console.log(chalk.yellow('   '), '• The FTP server might be temporarily down');
            console.log(chalk.yellow('   '), '• Try again in a few minutes');
            console.log(chalk.yellow('   '), '• Check if your IP is whitelisted (if required)');
        }
        
        if (error.message.includes('ECONNREFUSED')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), '• The FTP server might be blocking connections');
            console.log(chalk.yellow('   '), '• Check if you need to use a different port');
            console.log(chalk.yellow('   '), '• Verify if FTPS/SFTP is required instead of FTP');
        }
        
        console.log(chalk.cyan('\n📚 Documentation:'));
        console.log(chalk.gray('   https://github.com/yourusername/tetradkata-wp-content#deployment'));
        
        process.exit(1);
    } finally {
        client.close();
        console.log(chalk.gray('Connection closed.'));
    }
}

// Run the test
testFTPConnection();