# Changelog

## [Unreleased]

[Commit log](https://github.com/netzmacht/contao-worklfow/compare/2.0.0-beta1...master)


### Added

 - Add provider configuration for non default workflow types
 - Show success column in state history overview
 
### Changed

 - Rework property access by dropping `Entity` interfaces and introduce an `PropertyAccessManager`
 - Change namespace of the `UpdateEntityAction`
 - Only redirect from transition controller if new state is successful
 
### Fixed

 - Fix loading of available actions for a workflow in the backend
 - Fix getting id of database entities 
 - Use ArrayObject instead of array for database entities
