# 🎓 Automated Academic Assessment Generation Platform

> A web platform that leverages Artificial Intelligence to automate the generation of curriculum-aligned exams and structured correction guides for academic institutions.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Testing](#testing)
- [Academic Context](#academic-context)

---

## About the Project

This platform addresses the time-consuming manual process of academic assessment creation. Educators can upload their didactic materials (PDF, DOCX, or TXT), configure exam parameters, and have the system automatically generate complete examination papers and detailed correction guides — ready to export as PDF.

The system uses the **DeepSeek API** to generate content grounded in the uploaded materials, ensuring that questions accurately reflect the taught curriculum. A document chunking pipeline handles large files by splitting them into character-sized segments compatible with the language model's context window.

---

## Features

- 📄 **Document Upload** — supports PDF, DOCX, and TXT didactic materials
- 🤖 **AI-Powered Generation** — uses DeepSeek LLM to generate curriculum-aligned questions
- ⚙️ **Granular Configuration** — configure question types (multiple choice or open), difficulty levels (easy, medium, hard), number of questions, and points per question
- 📝 **Correction Guide** — automatic generation of structured marking schemes alongside each exam
- 📥 **PDF Export** — export exams and correction guides as professional PDF documents
- 🔒 **Authenticated Access** — secure login system for educators

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2 / Laravel 13.7.0 |
| Frontend | Blade Templates / Tailwind CSS 3 |
| Database | MySQL 8 |
| AI Integration | DeepSeek API (deepseek-chat) |
| PDF Parsing | smalot/pdfparser |
| DOCX Parsing | PhpOffice/PhpWord |
| PDF Generation | barryvdh/laravel-dompdf |
| Version Control | Git / GitHub |

---

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Educator (Browser)                    │
└─────────────────────────┬───────────────────────────────┘
                          │ HTTP
┌─────────────────────────▼───────────────────────────────┐
│              Laravel Application (MVC)                   │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │                   Services                       │   │
│  │  DeepSeekService   DocumentProcessorService      │   │
│  │  PdfGeneratorService                             │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │   Models    │  │    Views     │  │   Database    │  │
│  │  (Eloquent) │  │   (Blade)    │  │   (MySQL)     │  │
│  └─────────────┘  └──────────────┘  └───────────────┘  │
└─────────────────────────┬───────────────────────────────┘
                          │ API Call
┌─────────────────────────▼───────────────────────────────┐
│                   DeepSeek API                           │
│            (LLM Content Generation)                      │
└─────────────────────────────────────────────────────────┘
```

---

## Requirements

Before installing, make sure you have the following installed on your system:

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x and **npm**
- **MySQL** >= 8.0
- A **DeepSeek API key** — obtain at [platform.deepseek.com](https://platform.deepseek.com)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/felizardo1/academic-assessment-platform.git
cd academic-assessment-platform
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies

```bash
npm install
```

### 4. Copy the environment file

```bash
cp .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Configure the database

Create a MySQL database named `exam_generator` and update the `.env` file with your credentials (see [Configuration](#configuration) below).

### 7. Run database migrations

```bash
php artisan migrate
```

### 8. Create the storage symlink

```bash
php artisan storage:link
```

---

## Configuration

Open the `.env` file and update the following values:

```env
# Application
APP_NAME="Assessment Platform"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_generator
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# DeepSeek API
DEEPSEEK_API_KEY=your_deepseek_api_key_here
DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions
```

> ⚠️ **Never commit your `.env` file to version control.** It contains sensitive credentials including your DeepSeek API key.

---

## Usage

To run the application locally, two terminals must be open simultaneously.

**Terminal 1 — Laravel development server:**
```bash
php artisan serve
```

**Terminal 2 — Vite frontend assets:**
```bash
npm run dev
```

Then open your browser and navigate to:

```
http://localhost:8000
```

### Generating an exam

1. Register or log in as an educator
2. Create a new **Exam Session** and configure parameters:
   - Question type: `multiple_choice` or `open`
   - Difficulty: `easy`, `medium`, or `hard`
   - Number of questions and points per question
   - Thematic scope
3. Upload your didactic materials (PDF, DOCX, or TXT)
4. Click **Generate** and wait for the AI to process the content
5. Review the generated exam and correction guide
6. Export both documents as **PDF**

---

## Project Structure

```
academic-assessment-platform/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ExamController.php          # Exam CRUD and generation
│   ├── Models/
│   │   ├── Exam.php
│   │   ├── Document.php
│   │   └── Session.php
│   └── Services/
│       ├── DeepSeekService.php             # Prompt engineering and API calls
│       ├── DocumentProcessorService.php    # PDF/DOCX/TXT extraction and chunking
│       └── PdfGeneratorService.php         # PDF generation for exam and correction guide
├── database/
│   └── migrations/                         # Database schema migrations
├── resources/
│   └── views/
│       ├── exams/                          # Blade templates for exam configuration UI
│       └── pdf/
│           ├── exam.blade.php              # Student-facing exam PDF template
│           └── correction_guide.blade.php  # Educator-facing correction guide template
├── routes/
│   └── web.php                             # Application routes
├── tests/
│   └── Unit/
│       ├── DeepSeekServiceTest.php         # 10 unit tests
│       ├── DocumentProcessorServiceTest.php # 10 unit tests
│       └── PdfGeneratorServiceTest.php     # 7 unit tests
├── storage/
│   └── app/private/                        # Uploaded documents (private)
├── .env.example                            # Environment configuration template
├── composer.json
└── package.json
```

---

## Testing

The project includes 27 unit tests covering the three main services:

```bash
# Run all unit tests
php artisan test tests/Unit/

# Run individual test classes
php artisan test tests/Unit/DeepSeekServiceTest.php
php artisan test tests/Unit/DocumentProcessorServiceTest.php
php artisan test tests/Unit/PdfGeneratorServiceTest.php
```

| Test Class | Tests | Coverage |
|-----------|-------|----------|
| DeepSeekServiceTest | 10 | Prompt construction, JSON parsing, API mocking |
| DocumentProcessorServiceTest | 10 | Text extraction, chunking, edge cases |
| PdfGeneratorServiceTest | 7 | PDF generation, storage paths |

---

## Academic Context

This project was developed as part of the **Computer Science Project (DLMCSPCSP01)** portfolio at **IU Internationale Hochschule**.

**Research focus:** Application of Large Language Models and Natural Language Processing to automate academic assessment generation, combining document processing pipelines with structured prompt engineering to ensure curriculum alignment.
