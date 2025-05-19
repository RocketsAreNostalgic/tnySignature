#!/bin/bash

# Script to build frontend assets
echo "Building frontend assets..."

# Check if pnpm is installed
if ! command -v pnpm &> /dev/null; then
    echo "Error: pnpm is not installed. Please install pnpm first."
    echo "You can install it with: npm install -g pnpm"
    exit 1
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing pnpm dependencies..."
    pnpm install
    if [ $? -ne 0 ]; then
        echo "Error: Failed to install pnpm dependencies."
        exit 1
    fi
fi

# Run ESLint
echo "Running ESLint..."
pnpm run lint:js
if [ $? -ne 0 ]; then
    echo "Warning: ESLint found issues. You may want to fix these before building."
    # Don't exit with error as these might be acceptable
fi

# Run StyleLint
echo "Running StyleLint..."
pnpm run lint:css
if [ $? -ne 0 ]; then
    echo "Warning: StyleLint found issues. You may want to fix these before building."
    # Don't exit with error as these might be acceptable
fi

# Build assets
echo "Building assets with Vite..."
pnpm run build
if [ $? -ne 0 ]; then
    echo "Error: Failed to build assets."
    exit 1
fi

echo "Frontend assets built successfully!"
echo ""
echo "Minified files have been created in their respective asset directories."
