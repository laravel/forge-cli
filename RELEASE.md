# Release Instructions

- First, modify `config/app.php` version number.
- Next, run `./forge app:build` to compile the binary.
- After, commit both `config/app.php` and `builds/forge` files.
- Update the `CHANGELOG.md`.
- Finally, tag the new version of package.
- Push commits and tags.
