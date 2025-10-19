# OS Algorithms Lab ⚙️

A collection of essential Operating System algorithms with a clean UI and Swagger-powered API.

[![Web Page](https://i.postimg.cc/yNSSLBcb/260-1x-shots-so.png)](https://github.com/Nima8FT/Algorithm_OS)

[Get it here](https://github.com/Nima8FT/Algorithm_OS)

Version: [1.0.0]
## Table of Contents

1. [🚀 Overview](#1-overview)
2. [✨ Features](#2-features)
3. [🛠️ Installation](#3-installation)
4. [⚙️ Configuration](#4-configuration)
5. [💻 Usage](#5-usage)
6. [🤝 Contributing](#6-contributing)
7. [📝 License](#7-license)

---

### 1. Overview

In this repository, I’ve implemented most of the key Operating System algorithms, including CPU scheduling, memory management, page replacement, and the Banker’s algorithm. All implementations follow Clean Code principles and come with a simple UI. Additionally, Swagger is provided for those who want to interact with the API.

---

### 2. Features

**CPU Scheduling 🖥️**
- **FCFS** – First Come First Serve
- **SJF** – Shortest Job First
- **LJF** – Longest Job First
- **RR** – Round Robin
- **SRTF** – Shortest Remaining Time First
- **LRTF** – Longest Remaining Time First
- **HRRN** – Highest Response Ratio Next
- **Non-Preemptive** – Priority
- **Preemptive** – Priority

**Memory Allocation 🧠**
- **First Fit**
- **Best Fit**
- **Next Fit**
- **Worst Fit**

**Page Replacement 📄**
- **FIFO** – First In First Out
- **LIFO** – Last In First Out
- **LRU** – Last Recently Used
- **MRU** – Most Recently Used
- **LFU** – Last Frequently Used
- **MFU** – Most Frequently Used
- **Random Page Replacement**
- **Optimal Page Replacement**

**Banker’s Algorithm 💰**
    
---

### 3. Installation

```bash
git clone https://github.com/Nima8FT/Algorithm_OS.git
cd Algorithm_OS/API
composer install
composer update
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

### 4. Configuration

After installation, open one of the HTML files in your browser to start the application.
You can also visit api/documentation to access and use the Swagger API.

---

### 5. Usage

- Open one of the HTML files in your browser to use the web interface.
- Access API routes via Postman or any API client.
- Alternatively, visit api/documentation to explore and test the API using Swagger.
- All core OS algorithms (CPU scheduling, memory allocation, page replacement, Banker’s algorithm) can be tested through the UI or API.

---

### 6. Contributing

1. Fork this repository.
2. Create a branch: `git checkout -b my-feature`.
3. Make your changes and commit them: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin my-feature`.
5. Submit a pull request.

---

### 7. License

This project is open-sourced software

---
