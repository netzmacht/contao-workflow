# Changelog

## [Unreleased]

## [2.1.2] - 2020-03-31

### Fixed

 - Fix yaml syntax for Contao 4.9 compatibility
 - Use twig instead of templating component

## [2.1.1] - 2020-02-07

### Fixed

 - Fix error in OptionsListener when no options exists

## [2.1.0] - 2020-02-07

### Added

 - Add support for different transition types
 - Add support for workflow changes
 - Add support for conditional transitions
 - Allow to define property conditions depending on related models
 
### Fixed

 - Workaround issues with multi column wizard bundle [#14](https://github.com/netzmacht/contao-workflow/issues/14)

### Breaking

 - Changed transition form type. It requires the transition handler instead of the transition now
 - Change interface of TransitionFormBuilder
 - Only pass one transition form builder to the transition form type

## [2.0.0-rc3] - 2019-09-02

### Fixed
 
 - Only limit permissions if a permission is selected 
 - Introduce `ContaoModelSpecificationAwareSpecification` to fix specification implementation of the 
   `ContaoModelEntitySpecification`. Previous implementation wasn't compatible because both specifications interfaces
   aren't compatible so couldn't be applied both.

## [2.0.0-rc2] - 2019-02-28

### Added
 
  - Test against PHP *nightly* and *7.4snapshot*

### Fixed

  - Fix issue with broken routes not container *module* parameter
  - Register bundle `SensioFrameworkExtraBundle`

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

[Unreleased]: https://github.com/netzmacht/contao-workflow/compare/master...develop
[2.1.2]: https://github.com/netzmacht/contao-workflow/compare/2.0.1...2.1.2
[2.1.1]: https://github.com/netzmacht/contao-workflow/compare/2.0.0...2.1.1
[2.1.0]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc3...2.1.0
[2.0.0-rc3]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc2...2.0.0-rc3
[2.0.0-rc2]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc1...2.0.0-rc2
[2.0.0-rc1]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-beta1...2.0.0-rc1
