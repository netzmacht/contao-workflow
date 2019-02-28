# Changelog

## [Unreleased]

## [2.0.0-rc2] - 2019-02-28

### Added
 
  - Test against PHP *nightly* and *7.4snapshot*

### Fixed

  - Fix issue with broken routes not container *module* parameter

## [2.0.0-rc1] - 2019-07-02

### Added

 - Add provider configuration for non default workflow types
 - Show success column in state history overview
 - Add example for a custom implementation
 - Add `WorkflowType::class` config value in `AbstractWorkflowType#configure`
 
### Changed

 - Rework property access by dropping `Entity` interfaces and introduce an `PropertyAccessManager`
 - Change namespace of the `UpdateEntityAction`
 - Only redirect from transition controller if new state is successful
 - Improve backend not failing when invalid workflow definition is given
 - Only build active workflows for the workflow manager
 - Show error note if workflow couldn't be created in the workflow overview
 - Add module to route. Module is available as request attribute and not as query attribute now.
 
### Fixed

 - Fix loading of available actions for a workflow in the backend
 - Fix getting id of database entities 
 - Use ArrayObject instead of array for database entities
 
### Removed

 - Remove interface `Netzmacht\ContaoWorkflowBundle\Wokrlfow\Manager\Manager`
 - Remove `Netzmacht\ContaoWorkflowBundle\Wokrlfow\Manager\ContaoWorkflowManager`

[Unreleased]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc1...master
[2.0.0-rc2]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc1...2.0.0-rc2
[2.0.0-rc1]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-beta1...2.0.0-rc1
