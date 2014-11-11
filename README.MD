# Nooku Activities

## What is Nooku Activities?

Nooku activities is a re-usable component for logging and exposing activity streams. This component is built on top of the [Nooku Framework](https://github.com/nooku/nooku-framework) and may be used standalone or as the backbone of a more advanced activities application.

## Activities API

The component includes an API for working with activities. This API fully adheres to and extends the [Activity Streams specification v1.0](http://activitystrea.ms/specs/json/1.0/). JSON activity streams provided by the component are also fully compliant with the standard.  

## Requirements

- Nooku Framework 2 or newer.
- PHP 5.2 or newer.
- MySQL 5.

## Installation

Nooku Activities can and should be installed by using [composer](https://getcomposer.org/). For installing the component please complete the following steps:

- Create a `composer.json` file in the root directory of your Joomla site containing the following code:

### Development:

```json
{
    "require": {
        "nooku/activities-component": "dev-develop"
    },
    "minimum-stability": "dev"
}
```

### Stable:

```json
{
    "require": {
        "nooku/activities-component": "2.*"
    }
}
```

- Run `composer install` to install the component.

The component will get installed in the `vendor` folder of the root directory of your Joomla site. Nooku will make sure that the component is bootstrapped from that location and made available to other components extending it or just making use of it.

Out of the box, Nooku Activities will attempt to store activities in a database table named activities_activities. An SQL dump file may be found under the `/resources/install` directory for creating this database table. This database needs to be manually created for the component to function properly. At the moment we only support MySQL.

## Contributing

We appreciate any contribution to Nooku Activities, whether it is related to bugs, grammar, or simply a suggestion or
improvement. We ask that any contribution follows a few simple guidelines in order to be properly received.

We follow the [GitFlow](gitflow-model) branching model, from development to release. If you are not familiar with it,
there are several guides and tutorials online to learn about it.

There are a few things you must know before submitting a pull request:

- All changes need to be made against the `develop` branch. However, it is very well appreciated and highly suggested to start a new feature branch from `develop` and make your changes in this new branch. This way we can just checkout your feature branch for testing before merging it into `develop`.
- We will not consider pull requests made directly to the `master` branch.

## License 

Nooku Activities is open-source software licensed under the [GPLv3 license](https://github.com/nooku/nooku-framework/blob/master/LICENSE.txt).
