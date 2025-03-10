# System Zarządzania Harmonogramem Projektu

![Project Management System](https://via.placeholder.com/800x200.png?text=System+Zarzadzania+Harmonogramem+Projektu)

## 📖 Opis projektu
System Zarządzania Harmonogramem Projektu to narzędzie wspierające zarządzanie projektami. Umożliwia planowanie, monitorowanie i kontrolowanie zadań, co pozwala na efektywne zarządzanie czasem i zasobami.

## ✨ Funkcjonalności
System składa się z czterech głównych modułów:
- **Zarządzanie projektami**: Tworzenie i edycja projektów, przypisywanie zespołów, zarządzanie statusem.
- **Harmonogram**: Tworzenie zadań, przypisywanie osób, śledzenie postępu.
- **Słowniki**: Zarządzanie słownikami, filtrowanie i wyszukiwanie danych.
- **Powiadomienia**: Automatyczne przypomnienia o zadaniach i terminach.

## 🛠 Technologie
- **Frontend**: Angular
- **Backend**: Java (Spring Boot)
- **Baza danych**: PostgreSQL

## 🏗 Architektura systemu
System opiera się na trójwarstwowej architekturze:
1. **Frontend**: Angular UI
2. **Backend**: Spring Boot API
3. **Baza danych**: PostgreSQL

![Architecture Diagram](https://via.placeholder.com/800x400.png?text=Architecture+Diagram)

## 🚀 Instalacja i uruchomienie

### Wymagania systemowe
- **Java**: 17 lub nowsza
- **Node.js**: 18 lub nowszy
- **PostgreSQL**: 15 lub nowszy

### Kroki instalacji
1. **Backend**:
   - Skonfiguruj środowisko Java i Spring Boot.
   - Skonfiguruj połączenie z bazą danych w pliku `application.properties`.
   - Uruchom backend:
     ```bash
     ./mvnw spring-boot:run
     ```

2. **Frontend**:
   - Zainstaluj Angular CLI:
     ```bash
     npm install -g @angular/cli
     ```
   - W katalogu frontendowym zainstaluj zależności:
     ```bash
     npm install
     ```
   - Uruchom aplikację:
     ```bash
     ng serve
     ```

3. **Dostęp do aplikacji**:
   - Frontend: [http://localhost:4200](http://localhost:4200)
   - Backend API: [http://localhost:8080](http://localhost:8080)

## 📚 Dokumentacja API

### Endpointy projektów
- `GET /api/projects` - Pobierz listę projektów.
- `POST /api/projects` - Utwórz nowy projekt.
- `PUT /api/projects/{id}` - Zaktualizuj projekt.
- `DELETE /api/projects/{id}` - Usuń projekt.

### Endpointy zadań
- `GET /api/tasks` - Pobierz listę zadań.
- `POST /api/tasks` - Utwórz nowe zadanie.
- `PUT /api/tasks/{id}` - Zaktualizuj zadanie.
- `DELETE /api/tasks/{id}` - Usuń zadanie.

## 🗓 Harmonogram implementacji
1. **Etap 1 - Funkcjonalności krytyczne (P1)**:
   - Konfiguracja projektu Angular i Spring Boot.
   - Implementacja CRUD dla projektów i zadań.
   - Wykres Gantta.

2. **Etap 2 - Funkcjonalności ważne (P2)**:
   - Uwierzytelnianie użytkowników.
   - Zarządzanie słownikami.
   - System powiadomień.

3. **Etap 3 - Funkcjonalności dodatkowe (P3)**:
   - Integracja z kalendarzem.
   - Funkcje drag & drop.

## 👥 Role użytkowników
- **Project Manager**: Tworzenie projektów, przypisywanie zespołów, monitorowanie postępu.
- **Team Member**: Przeglądanie i aktualizowanie zadań, otrzymywanie powiadomień.
- **System Administrator**: Zarządzanie słownikami, konfiguracja powiadomień, zarządzanie uprawnieniami.
- **Client**: Śledzenie postępu projektu, otrzymywanie powiadomień.

## 📊 Model danych
![Data Model Diagram](https://via.placeholder.com/800x400.png?text=Data+Model+Diagram)

## 📝 Licencja
Projekt jest dostępny na licencji MIT. Szczegóły znajdują się w pliku `LICENSE`.

---

> **Wskazówka**: Jeśli masz pytania lub sugestie dotyczące projektu, zapraszamy do otwierania zgłoszeń w sekcji [Issues](https://github.com/your-repo/issues).

🎉 Dziękujemy za zainteresowanie naszym projektem!
