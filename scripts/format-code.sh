#!/bin/bash

# Script to format code according to WordPress standards
echo "Running code formatting tools in the correct order..."

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Error: Composer is not installed. Please install Composer first."
    exit 1
fi

# Step 1: Check PHP syntax
echo "Step 1: Checking PHP syntax..."
composer lint
if [ $? -ne 0 ]; then
    echo "Error: PHP syntax check failed. Please fix the errors before continuing."
    exit 1
fi

# Step 2: Run PHP CS Fixer first to handle basic formatting
echo "Step 2: Running PHP CS Fixer for basic formatting..."
composer cs
if [ $? -ne 0 ]; then
    echo "Error: PHP CS Fixer failed. Please check the errors."
    exit 1
fi

# Step 3: Run PHPCBF to fix WordPress coding standards issues
# This should be the last formatting step since it handles indentation in mixed HTML/PHP files better
echo "Step 3: Running PHPCBF to fix WordPress coding standards issues..."
composer standards:fix
if [ $? -ne 0 ]; then
    echo "Warning: PHPCBF encountered some issues that couldn't be automatically fixed."
    # Don't exit with error as these might be acceptable
fi

# Step 4: Final check with PHPCS
echo "Step 4: Final check with PHPCS..."
composer standards
if [ $? -ne 0 ]; then
    echo "Warning: Some WordPress coding standards issues remain."
    echo "You may need to fix these manually or adjust your configuration."
    # Don't exit with error as these might be acceptable
fi

echo "Code formatting complete!"
