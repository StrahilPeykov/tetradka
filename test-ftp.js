#!/usr/bin/env node

const ftp = require('basic-ftp');
const chalk = require('chalk');

const config = {
    host: 'ftpupload.net',
    user: 'if0_39618140',
    password: 'tetradkatapass1',
    secure: false
};

async function testFTPConnection() {
    console.log(chalk.bold.cyan('\n🔌 Testing FTP Connection to InfinityFree\n'));
    
    const client = new ftp.Client();
    client.ftp.verbose = false;
    
    try {
        console.log(chalk.blue('ℹ'), 'Connecting to FTP server...');
        
        await client.access({
            host: config.host,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        
        console.log(chalk.green('✅'), 'FTP connection successful!');
        
        // Test directory navigation
        console.log(chalk.blue('ℹ'), 'Testing directory access...');
        
        await client.cd('/htdocs/');
        console.log(chalk.green('✅'), 'htdocs directory accessible');
        
        await client.cd('wp-content/');
        console.log(chalk.green('✅'), 'wp-content directory accessible');
        
        await client.cd('themes/');
        console.log(chalk.green('✅'), 'themes directory accessible');
        
        // Check if our theme directory exists
        try {
            await client.cd('tetradkata-theme/');
            console.log(chalk.green('✅'), 'tetradkata-theme directory exists');
        } catch (error) {
            console.log(chalk.yellow('⚠'), 'tetradkata-theme directory not found - will be created during deployment');
        }
        
        // Get current working directory
        const pwd = await client.pwd();
        console.log(chalk.blue('ℹ'), `Current remote directory: ${pwd}`);
        
        // List some files to verify access
        console.log(chalk.blue('ℹ'), 'Listing remote files...');
        const list = await client.list();
        console.log(chalk.green('✅'), `Found ${list.length} items in current directory`);
        
        console.log(chalk.bold.green('\n🎉 All FTP tests passed! Ready for deployment.\n'));
        
    } catch (error) {
        console.log(chalk.red('❌'), 'FTP test failed:');
        console.log(chalk.red('   '), error.message);
        
        if (error.message.includes('530')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), 'Check your FTP username and password');
            console.log(chalk.yellow('   '), 'Make sure your InfinityFree account is active');
        }
        
        if (error.message.includes('timeout')) {
            console.log(chalk.yellow('\n💡 Tips:'));
            console.log(chalk.yellow('   '), 'Check your internet connection');
            console.log(chalk.yellow('   '), 'Try again in a few minutes');
        }
        
        process.exit(1);
    } finally {
        client.close();
    }
}

// Run the test
testFTPConnection();