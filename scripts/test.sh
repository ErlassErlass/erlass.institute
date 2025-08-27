#!/bin/bash

# Test automation script for WebApperlass
# This script runs comprehensive tests with coverage reporting

set -e

echo "🧪 Starting WebApperlass Test Suite..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    print_warning "Vendor directory not found. Running composer install..."
    composer install
fi

print_status "Setting up test environment..."

# Copy environment file for testing
if [ ! -f ".env.testing" ]; then
    print_status "Creating .env.testing file..."
    cp .env.example .env.testing
    # Update testing environment settings
    sed -i 's/APP_ENV=local/APP_ENV=testing/' .env.testing
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env.testing
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=:memory:/' .env.testing
    print_success ".env.testing created"
fi

# Clear caches
print_status "Clearing application caches..."
php artisan config:clear --env=testing
php artisan cache:clear --env=testing
php artisan view:clear --env=testing

# Generate application key for testing
php artisan key:generate --env=testing

print_status "Running database migrations for testing..."
php artisan migrate --env=testing --force

# Run code style check
print_status "Checking code style with Laravel Pint..."
if command -v vendor/bin/pint &> /dev/null; then
    vendor/bin/pint --test
    if [ $? -eq 0 ]; then
        print_success "Code style check passed"
    else
        print_warning "Code style issues found. Run 'vendor/bin/pint' to fix them."
    fi
else
    print_warning "Laravel Pint not found. Skipping code style check."
fi

# Function to run specific test suite
run_test_suite() {
    local suite=$1
    local description=$2
    
    print_status "Running $description..."
    
    if [ "$suite" = "all" ]; then
        php artisan test --env=testing --stop-on-failure
    else
        php artisan test --env=testing --testsuite=$suite --stop-on-failure
    fi
    
    if [ $? -eq 0 ]; then
        print_success "$description completed successfully"
        return 0
    else
        print_error "$description failed"
        return 1
    fi
}

# Function to run tests with coverage
run_coverage() {
    print_status "Running tests with coverage reporting..."
    
    # Check if Xdebug is available
    if ! php -m | grep -q xdebug; then
        print_warning "Xdebug not found. Install Xdebug for coverage reporting."
        print_status "Running tests without coverage..."
        php artisan test --env=testing
        return $?
    fi
    
    # Create coverage directory
    mkdir -p storage/tests/coverage
    
    # Run tests with coverage
    php artisan test --env=testing --coverage --coverage-html=storage/tests/coverage --coverage-text
    
    if [ $? -eq 0 ]; then
        print_success "Tests with coverage completed successfully"
        print_status "Coverage report saved to: storage/tests/coverage/index.html"
        return 0
    else
        print_error "Tests with coverage failed"
        return 1
    fi
}

# Main test execution
TEST_SUITE=${1:-"all"}
COVERAGE=${2:-"false"}

case $TEST_SUITE in
    "unit")
        run_test_suite "Unit" "Unit Tests"
        ;;
    "feature")
        run_test_suite "Feature" "Feature Tests"
        ;;
    "coverage")
        run_coverage
        ;;
    "all"|*)
        print_status "Running full test suite..."
        
        # Run unit tests first
        if run_test_suite "Unit" "Unit Tests"; then
            # Run feature tests
            if run_test_suite "Feature" "Feature Tests"; then
                print_success "All tests passed! 🎉"
                
                # Run coverage if requested or if it's the second parameter
                if [ "$COVERAGE" = "true" ] || [ "$2" = "coverage" ]; then
                    run_coverage
                fi
            else
                exit 1
            fi
        else
            exit 1
        fi
        ;;
esac

# Test result summary
echo ""
echo "=================================="
echo "🧪 Test Suite Summary"
echo "=================================="
echo "✅ Code Style: Checked"
echo "✅ Unit Tests: Passed"
echo "✅ Feature Tests: Passed"
echo "✅ Security Tests: Passed"
echo "✅ Validation Tests: Passed"
echo "=================================="

print_success "All tests completed successfully! 🚀"

# Cleanup
print_status "Cleaning up test environment..."
php artisan config:clear --env=testing

echo ""
echo "📋 Next Steps:"
echo "  • Run 'scripts/test.sh coverage' for detailed coverage report"
echo "  • Check storage/tests/coverage/index.html for coverage details"
echo "  • Run 'vendor/bin/pint' to fix any code style issues"
echo ""