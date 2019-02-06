# Changelog

## [Unreleased]

[Commit log](https://github.com/netzmacht/contao-worklfow/compare/2.0.0-beta1...master)


### Added

 - Add provider configuration for non default workflow types
 
### Changed

 - Rework property access by dropping `Entity` interfaces and introduce an `PropertyAccessManager`
 - Change namespace of the `UpdateEntityAction`
 
### Fixed

 - Fix loading of available actions for a workflow in the backend
 - Fix getting id of database entities 
