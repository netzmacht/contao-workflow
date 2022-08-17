# Changelog

## [Unreleased]

## [2.4.0] - 2022-08-17

## Added

 - Add backend module icon
 - Enable duplicating of the whole workflow

## Changed

 - Use symfony contracts where possible
 - Require at least Symfony 4.4 and Contao 4.9
 - Support PHP 8

## Breaking
 - Rewrite voters. Steps and transitions has to be passed as name with `step:` or `transition:` prefix

## [2.3.4] - 2021-03-01

### Fixed

 - Fix transition action migration script

## [2.3.3] - 2020-11-30

### Fixed

 - Fix transition migration. Runonce wasn't registered
 - Catch AuthenticationCredentialsNotFoundException exception in the permission conditions

## [2.3.2] - 2020-11-25

### Fixed

 - Fix customization of note payload name

## [2.3.1] - 2020-11-25

### Fixed

 - Redirect for non started workflows is broken. Create explicit view for it.

## [2.3.0] - 2020-11-24

### Added

 - Allow creating transition specific actions
 - Add interface `DataWareActionFromBuilder` to allow actions define default form values
 - Add update entity action for dca using `useRawRequestData` flag
 - Add option to store current step permission
 - Add frontend module workflow transition
 - Add option to auto assign a workflow of a default type
 - Provide `userId` and the user model as `user` for the `UpdatePropertyAction` expression
 - Add ability to customize payload name of the note action note
 - Provide german translation
 
### Changed
 
 - Actions can be defined on transition level. Workflow related actions are supported yet and can be referenced.
 - Transitions are triggered as post request now 
 - Permissions for admins needs to be explicit granted
 
### Fixed

 - Fix security system for frontend user and guests
 - Readd option to limit permissions for transitions
 - Fix removal of permissions
 - Do not show transition marked as hidden as submit button
 - Provide context for Contao models in the `AddFormattedEntityNotificationTokensListener` listener
 - Fixed `payload_*` and `properties_*` notification tokens were not set
 - Fixed `admin_email` payload token was missing

## [2.2.4] - 2020-10-08

### Fixed

 - Disable input encoding / filter at all for `property_value`
 
## [2.2.3] - 2020-07-03

### Fixed

 - Property conditions couldn't be deleted

## [2.2.2] - 2020-07-01

### Fixed
 
 - Fix `errors.html.twig` template for twig 2.0
 - Do not communicate errors when detecting matching conditional transition

## [2.2.1] - 2020-06-23

### Fixed
 - Restore compatibility with Contao 4.4 and Symfony 3.4 ([#19](https://github.com/netzmacht/contao-workflow/issues/19))

## [2.2.0] - 2020-06-03

### Added

 - Add UpdateProperty action
 - Add ability to register own property accessors
 - Track changes of related models
 
### Changed
 - Introduce a PropertyAccessorFactory
 
### Fixed

 - Explicit declare used dependencies 

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
[2.3.4]: https://github.com/netzmacht/contao-workflow/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/netzmacht/contao-workflow/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/netzmacht/contao-workflow/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/netzmacht/contao-workflow/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/netzmacht/contao-workflow/compare/2.2.4...2.3.0
[2.2.4]: https://github.com/netzmacht/contao-workflow/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/netzmacht/contao-workflow/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/netzmacht/contao-workflow/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/netzmacht/contao-workflow/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/netzmacht/contao-workflow/compare/2.1.2...2.2.0
[2.1.2]: https://github.com/netzmacht/contao-workflow/compare/2.0.1...2.1.2
[2.1.1]: https://github.com/netzmacht/contao-workflow/compare/2.0.0...2.1.1
[2.1.0]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc3...2.1.0
[2.0.0-rc3]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc2...2.0.0-rc3
[2.0.0-rc2]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-rc1...2.0.0-rc2
[2.0.0-rc1]: https://github.com/netzmacht/contao-workflow/compare/2.0.0-beta1...2.0.0-rc1
