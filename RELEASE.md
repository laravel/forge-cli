# Release Instructions

 1. Pull down the latest changes on the current stable branch
 2. Update and commit the [CHANGELOG.md](./CHANGELOG.md) file
 3. Update the version in [`config/app.php`](./config/app.php)
 4. Compile the binary with

```zsh
./forge app:build
```

 5. Commit all changes
 6. Tag a new version of the package
 7. Push all commits and the new tag to GitHub
 8. Create a new GitHub release with the same release notes from CHANGELOG.md
