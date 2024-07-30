
# Symfony CSRF Token Bug Reproducer

This project demonstrates a bug related to the CSRF token validation in a Symfony application. When a form is submitted with a CSRF token, the token is retrieved and polluted using a custom service (`CsrfPolluter`). The polluted CSRF token is then validated as valid.

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/EC7-LandHugger/symfony-csrf.git
   cd symfony-csrf
   ```
2. Install dependencies: `composer install`.
3. Run the Symfony server: `symfony serve -d`

## Usage

1. Open your browser and navigate to `http://localhost:8000/login`.

## Bug Details

### Steps to Reproduce

1. Submit the login form.
2. See the details of the bug displayed below the form.

### What does `CsrfPolluter` do?

The CSRF token is retrieved and polluted by the `CsrfPolluter` service. The `CsrfPolluter` pollutes the token by changing each character in the token to its next character. The characters include uppercase letters, lowercase letters, and digits (0-9). For example:
   - `1` is replaced with `2`
   - `2` is replaced with `3`
   - `a` is replaced with `b`
   - `b` is replaced with `c`
   - `A` is replaced with `B`
   - `B` is replaced with `C`

> Note: The `Z`, `z`, and `9` characters are untouched during the pollution.

> Note: The polluted CSRF token is validated using: `$this->isCsrfTokenValid('authenticate', $pollutedCsrfToken);`.

The replacement stops when it encounters a symbol.

### Actual Behavior

1. Despite the pollution, the CSRF token validation returns `true`.

### Expected Behavior

The validation should return `false` for the polluted CSRF token.

## Contributing

Contributions are welcome! Please make this more minimal and understandable if possible.

## Contact

You can reach me at easwarchinraj@gmail.com.
