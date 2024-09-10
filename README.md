# Symfony Docker Project

This project is a Symfony-based application that utilizes Docker for development and testing. Below you'll find instructions on how to set up the project, run it, and execute tests.

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [PHPUnit](https://phpunit.de/getting-started/phpunit-10.html) (for running tests)

## Getting Started

### 1. Clone the Repository

First, clone the repository:

```bash
git clone <repository-url>
cd <repository-directory>
```

### 2. Set Up Environment Variables

```bash
cp .env.test .env
```

### 3. Build and Start the Docker Containers

```bash
docker-compose up --build
```

### 4. Access the Symfony Application

```bash
[docker-compose up --build](http://localhost)
```

### 5.  Run Tests

```bash
docker-compose exec php ./vendor/bin/phpunit
```

License
This project is licensed under the MIT License.

Contributing
Contributions are welcome! Please refer to the CONTRIBUTING.md file for guidelines.
