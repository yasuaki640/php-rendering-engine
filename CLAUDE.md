# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Structure

This is a PHP CLI application with PSR-4 autoloading:
- `src/` - Contains the main application classes with namespace `Yasuaki640\PhpRenderingEngine\`
- `bin/hello` - CLI executable entry point
- `composer.json` - Defines autoloading, binary path, and project metadata

## Commands

### Installation
```bash
composer install
```

### Running the CLI
```bash
./bin/hello
# or
php bin/hello
```

### Making the binary executable
```bash
chmod +x bin/hello
```

## Architecture

The application follows a simple CLI pattern:
- `CLI` class in `src/CLI.php` contains the main application logic
- Binary executable in `bin/hello` bootstraps the autoloader and instantiates the CLI class
- Currently implements a basic "hello world" functionality in the `run()` method