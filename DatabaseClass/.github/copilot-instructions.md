# Copilot Instructions for PHPClass DatabaseClass

## Project Overview
Single-file PHP application for sales management (`Gestion.php`). Implements basic CRUD operations for client management with direct database integration. Uses PDO with MariaDB on XAMPP (port 3377).

## Architecture & Key Patterns

### Database Initialization Pattern
- **Location**: [Gestion.php](Gestion.php#L15-L25)
- Connect to server first without dbname, execute `CREATE DATABASE IF NOT EXISTS`, then reconnect to the database
- Auto-creates tables with `CREATE TABLE IF NOT EXISTS` on startup
- Uses PDO prepared statements for all user input to prevent SQL injection

### CRUD Implementation
- **Create**: Form submission via POST with `ajouter` button → `INSERT` via prepared statement (lines 75-82)
- **Read**: Single `SELECT * FROM client` query executed on page load (line 120)
- **Update**: POST `modifier` button triggers `UPDATE WHERE id_client = ?` (lines 103-113)
- **Delete**: GET parameter `supprimer` directly in SQL (line 97) - **BUG: Not using prepared statement**

### Field Structure
Client table includes: `id`, `nom`, `prenom`, `email` (unique), `telephone`, `date_inscrip`, `adresse` (added via ALTER TABLE)

### Frontend-Backend Binding
JavaScript function `remplir()` (line 200) populates form fields from table row data for edit functionality. Form uses hidden `id` field to distinguish add vs. modify operations.

## Critical Issues & Conventions

### Security Concerns
1. **SQL Injection Risk**: Delete operation (line 97) uses string interpolation instead of prepared statements: `DELETE FROM client WHERE id_client = $id`
   - **Fix**: Use prepared statement: `$pdo->prepare("DELETE FROM client WHERE id_client = ?")->execute([$id])`
2. **XSS Risk**: All table output uses `<?= ?>` without escaping: `<?= $c['nom'] ?>`
   - **Convention**: Use `<?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?>` for all user data

### Database Configuration
Hardcoded credentials in main file (lines 9-13). Do NOT move these to environment variables without refactoring the structure.

### Error Handling
Uses try-catch for connection errors but silently fails on `ALTER TABLE` (line 65-67) to prevent duplicate column errors.

## Development Guidelines

1. **Keep monolithic structure**: This is intentionally a single-file application for learning purposes
2. **Maintain PDO pattern**: Always use prepared statements for dynamic SQL (no exceptions)
3. **Use POST for mutations**: Add/modify/delete via form submission, not GET except for delete link (which should be migrated)
4. **Test database port**: Default MySQL runs on 3306; this uses 3377 (XAMPP configured)
5. **HTML embedded in PHP**: Frontend is in same file; no separation of concerns by design

## Testing & Debugging
- Start XAMPP with MySQL/MariaDB on port 3377
- Database `gestion_vente` auto-creates on first page load
- Check browser console for JavaScript errors in `remplir()` function
- Monitor PDO exceptions for connection or SQL errors in error logs
