# 🦑 KrakenEnvKeeper - Securely Manage Environment Variables in PHP Projects

KrakenEnvKeeper is a Composer package that helps you securely load and access environment variables from a dedicated `.env` file within your PHP applications. It provides a convenient and reliable way to manage sensitive data.

## ✨ Features

- 🔒 Loads environment variables securely from a designated `.env` file.
- 🚨 Catches missing variables and throws exceptions for better error handling.
- 🚀 Optimizes performance by caching loaded variables.
- 👀 Monitors the `.env` file for changes, automatically reloading variables when necessary.

## 📝 Additional Notes

### 🛡️ Security Considerations

- ⚠️ **Never** store your `.env` file in version control systems like Git.
- 🔐 Consider using environment variable encryption to further enhance security, especially in production environments. Encryption adds an extra layer of protection for your sensitive data.

### ⚡ Error Handling

- The `get` method throws an exception for missing variables. If a missing variable might be expected in some cases, provide a custom default value to avoid these exceptions. This can improve code flow and readability.

### 🗂️ Customizable File Path

- The constructor allows for customization of the `.env` file location. By default, it looks for a file named `.env` in the root directory of the project. You can adjust this to fit your project structure.

### 💾 Cache Optimization

- The cache helps improve performance by preventing unnecessary file reloads. For complex setups, you might consider alternative caching mechanisms for optimized performance.

## ⚠️ Development Mode Notice

Please note that this is the development version of KrakenEnvKeeper. Functionality and APIs might be subject to change. Consider using a stable release for production environments.

## 🚀 Installation

1. Require the package using Composer:

    ```bash
    composer require devkraken/kraken-env-keeper
    ```

2. (Optional) Customize the .env file path:

By default, KrakenEnvKeeper looks for a file named .env in the root directory of your project. If you prefer a different location, update the constructor call in your code:

## 🔧 Usage

1. Load environment variables:
    
    ```php
    $envKeeper = new DevKraken\KrakenEnvKeeper();
    $envKeeper->load();
    ```
2. Access environment variables:
* Use the `get` method to retrieve a specific variable:

    ```php
    $apiKey = $envKeeper->get('API_KEY');
    ```
* Use the has method to check if a variable exists:
    ```php
    if ($envKeeper->has('DEBUG_MODE')) {
        // Enable debugging if set
    }
    ```
## 🤝 Contributing

We welcome contributions to improve KrakenEnvKeeper! Here's how you can get involved:
1. Fork the repository: Visit the project's GitHub repository (link to be provided later) and fork it to your account.
2. Create a branch: Create a new branch for your changes.
3. Implement your changes: Make code modifications, add tests, and improve documentation as needed.
4. Commit your changes: Commit your changes with clear and concise messages.
5. Create a pull request: Open a pull request on the upstream repository, outlining your proposed changes.

## 📄 License
This project is licensed under the MIT License. Please refer to the LICENSE file for details.
