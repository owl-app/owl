<p align="center">
    <img src="https://raw.githubusercontent.com/owl-app/time-tracker/refs/heads/main/libs/app/core/src/assets/logo.webp" width="150px" alt="Owl logo" />
</p>

**Owl** is an advanced, modular invoicing and business management platform built on **Sylius architecture** and Symfony 6.4. The system provides a comprehensive solution for invoice generation, business document management, and financial operations with advanced role-based access control (RBAC), and modern user interface.

## 🚀 Key Features

Owl is **primarily an invoicing system** that leverages Sylius's proven e-commerce architecture to deliver robust business functionality beyond traditional e-commerce needs.

### Core Invoice Management System
- **Multi-type Invoices** - Sales invoices, proforma invoices, and correction invoices
- **Advanced Tax System** - VAT calculations with tax rate snapshots for compliance
- **Multiple Payment Methods** - Wire transfer, cash, card, credit, check, and custom methods
- **Buyer & Seller Management** - Complete customer and vendor data management
- **Line Items & Calculations** - Detailed invoice line items with automatic net/gross calculations
- **Invoice Series & Numbering** - Configurable invoice numbering with series management
- **Multi-currency Support** - Exchange rate snapshots for international transactions
- **Payment Tracking** - Invoice payment status management (pending, completed)

### Business Management Modules
- **AdminBundle** - Administrative dashboard
- **UserBundle** - User management and authentication
- **RbacBundle** - Role-based access control system
- **CategoryBundle** - Category management
- **FileBundle** - File and media management
- **CompanyBundle** - Company information management
- **ContractorBundle** - Contractor/vendor management
- **LocationBundle** - Location and address management
- **NotificationBundle** - Notification system
- **SettingBundle** - Application configuration
- **UiBundle** - User interface components

### Technology Stack
- **Backend**: PHP 8.2+, Symfony 6.4, Doctrine ORM
- **Architecture**: Based on **Sylius Framework** architecture patterns
- **Frontend**: JavaScript (ES6+), Stimulus, Webpack Encore, Tom Select
- **Database**: MySQL 5.7/8.0
- **Testing**: PHPUnit, Playwright (E2E)
- **Code Quality**: PHPStan, ECS (Easy Coding Standard)
- **Containerization**: Docker, Docker Compose

## 🧾 Invoice System Features

### Invoice Types
- **Sales Invoices** (`sales`) - Standard billing documents

### Tax Management
- **VAT Calculations** - Automatic tax calculations
- **Tax Rate Snapshots** - Historical tax rate preservation for compliance
- **Net/Gross Calculations** - Flexible calculation from net or gross amounts
- **Multi-tax Support** - Different tax rates per line item

### Business Features
- **Multi-currency Support** - Exchange rate snapshots for foreign transactions
- **Invoice Numbering** - Configurable series and sequential numbering
- **Line Item Management** - Detailed invoice positions with quantities and units
- **Payment Tracking** - Invoice payment status monitoring
- **Buyer/Seller Profiles** - Complete customer and vendor management

## 🔒 RBAC System

Owl utilizes an advanced Role-Based Access Control (RBAC) system using the yiisoft/rbac library. The system enables:

- Role and permission definitions
- Role hierarchies
- Business rules
- Dynamic permission assignments

## 📋 System Requirements

- PHP 8.2 or higher
- Node.js 20+ or 22+
- MySQL 5.7+ or 8.0+
- Composer 2.4+
- Docker and Docker Compose (for development environment)

## 🛠️ Installation

### Docker

1. **Clone the repository**
```bash
git clone [repository-url]
cd owl
```

2. **Configure environment variables**
```bash
cp .env .env.local
# Edit .env.local as needed
```

3. **Start the application**
```bash
docker-compose up -d
```

4. **Access the application**
- Application: http://localhost:8080
- PHPMyAdmin: http://localhost:8088
- Mailhog: http://localhost:8025

### Manual

```shell
# Install required dependencies
$ composer install

# Copy .env file and change the database connection settings
$ cp .env .env.local

# Create database
$ php bin/console doctrine:database:create

# Create database schema
$ php bin/console doctrine:schema:create

# Load fixtures
$ php bin/console sylius:fixtures:load

# Install packages
$ yarn install

# Build dev
$ yarn encore dev

# Create theme dir for admin
$ mkdir -p public/_themes/owl/admin

# Install assets
$ php bin/console sylius:theme:assets:install public/_themes/owl/admin

# Start server
$ symfony serve:start
```

## 🧪 Testing

### Unit Tests (PHPUnit)
```bash
vendor/bin/phpunit
```

### E2E Tests (Playwright)
```bash
npm run test:e2e
npm run test:e2e:headed  # With GUI
npm run test:e2e:debug   # Debug mode
```

## 📊 Code Quality

### Static Analysis (PHPStan)
```bash
vendor/bin/phpstan analyse
```

### Coding Standards (ECS)
```bash
vendor/bin/ecs check
vendor/bin/ecs check --fix  # Auto-fix issues
```

### JavaScript Linting
```bash
npm run lint
```

## 📁 Project Structure

```
owl/
├── src/
│   ├── Kernel.php
│   └── Owl/
│       ├── Bundle/          # Application modules
│       │   ├── InvoiceBundle/   # Invoice management
│       │   ├── AdminBundle/     # Administration panel
│       │   ├── UserBundle/      # User management
│       │   └── ...              # Other business modules
│       ├── Component/       # Business logic components
│       │   ├── Invoice/         # Invoice domain logic
│       │   └── ...              # Other domain components
│       ├── Bridge/          # External library integrations
├── config/                  # Application configuration
├── public/                  # Public files
├── templates/              # Twig templates
├── themes/                 # Application themes
├── translations/           # Translations
├── migrations/             # Database migrations
├── tests/                  # Tests
├── e2e/                    # E2E tests
└── assets/                 # Frontend assets
```

## 🏗️ Sylius Architecture

Owl is built on **Sylius architecture principles**, leveraging:

- **Resource-based architecture** - Each business entity is a resource
- **State machine workflows** - For invoice status management
- **Grid system** - For data listing and management
- **Repository patterns** - For data access
- **Factory patterns** - For object creation
- **Event-driven architecture** - For extensibility

### Sylius Components Used
- **SyliusResourceBundle** - Resource management
- **SyliusGridBundle** - Data grids
- **SyliusThemeBundle** - Theming system
- **SyliusMailerBundle** - Email management
- **SyliusFixturesBundle** - Data fixtures

## 🚀 Deployment

### Production

1. **Build Docker images**
```bash
docker build --target owl_php_prod -t owl:php-prod .
docker build --target owl_nginx -t owl:nginx .
```

2. **Production environment variables**
```bash
cp .env.prod .env.local
# Configure production values
```

3. **Run database migrations**
```bash
docker run --rm owl:php-prod bin/console doctrine:migrations:migrate --no-interaction
```

### Environments

- **dev** - Development
- **test** - Testing
- **prod** - Production

### Conventions

- PSR-4 autoloading
- Symfony best practices
- Sylius coding standards
- PHPDoc documentation

## 👨‍💻 Author

**Paweł Kęska**  
Email: projekty@pawelkeska.eu

## 📄 License

Owl is completely free and released under the MIT License.

If you have any additional details or specific sections you'd like to include, please let me know!

## 🐛 Bug Reports

If you find any bugs or issues, please report them through the Issue tracker system.

## 🔧 Support

For additional help and support, please contact the project author.
