# donations-tracking

**Project Description:**
CLI application to track charities donations

## Setup Instructions

### 1. Instal & Run XAMPP

Install xampp if don't have and runt MySQL and Apache

### 2. Configure Database Connection

Configure your database connection file config.php with your credentials.

```php
<?php
$rootDsn = 'your-database';
$rootUsername = 'your-username';
$rootPassword = 'your-password';
$dbName = 'your-database-name';
```

### 3. Run Migration

Navigate to directory .\CLI\ and run migration to create database with tables

```bash
php .\MigrationRunner.php
```

### 4. Commands to interact with database

Add charities using csv file. Add files path to

```bash
php ImportCharities.php <csvFilePath>
```

Navigate to .\CLI\charity\ to interact with charity database

Add charity

```bash
 php AddCharity.php <name> <representative_email>
```

Delete charity

```bash
 php DeleteCharity.php <charityId>
```

Update charity

```bash
 php UpdateCharity.php <charityId> <name> <representativeEmail>
```

View charities

```bash
 php .\ViewAllCharities.php
```

Navigate to .\CLI\donation\ to interact with donations database

Add donation

```bash
 php AddDonation.php <charityId> <amount> <donorName> <dateTime>
```
