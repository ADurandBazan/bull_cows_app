<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Description of the "Bulls and Cows" REST API

This REST API is designed for the popular game "Bulls and Cows," where the computer generates a 4-digit numerical secret code and the user must guess it within a time interval.

## Key Features:

The computer randomly generates a 4-digit numerical secret code.
The user can submit guesses for the secret code.
The API responds with the number of "bulls" (digits correct and in the correct position) and "cows" (digits correct but in the wrong position) for each guess.
The user has a limited time to guess the secret code.
The API documentation is accessible at the "/api/documentation" endpoint.

## API Endpoints:

POST /api/game/start: Initiates a new game and generates a secret code.
POST /api/game/guess: Allows the user to submit a guess for the secret code.
GET /api/documentation: Provides detailed documentation for the API.

## Technologies Used:

Programming Language: Php
Web Framework: Laravel
Database: SQLite

