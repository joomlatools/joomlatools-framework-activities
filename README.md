# Nooku Activities

## What is Nooku Activities?

Nooku activities is a re-usable component for logging and exposing activity streams. This component is built on top of
[Nooku Framework](https://github.com/nooku/nooku-framework) and may be used standalone or as the backbone of a more
advanced activities application.

## Activities API

The component includes an API for working with activities. This API fully adheres to and extends the [Activity Streams
specification v1.0](http://activitystrea.ms/specs/json/1.0/). JSON activity streams provided by the component are also
fully compliant with the standard.

## Requirements

- Nooku Framework 2 or newer.
- PHP 5.2 or newer.
- MySQL 5.

## Installation

Nooku Activities can and should be installed by using [Composer](https://getcomposer.org/).

Go to the root directory of your Joomla installation in command line and execute this command:

```
composer require nooku/nooku-activities:2.*
```

The component will be installed in the `vendor` folder of the root directory of your Joomla site. Nooku will make sure
that the component is bootstrapped from that location and made available to other components extending it or just making
use of it.

Out of the box, Nooku Activities will attempt to store activities in a database table named `activities`. An
SQL dump file may be found under the `/resources/install` directory for creating this database table. This database
needs to be manually created for the component to function properly. At the moment we only support MySQL.

## Contributing

Nooku Activities is an open source, community-driven project. Contributions are welcome from everyone. 
We have [contributing guidelines](CONTRIBUTING.md) to help you get started.

## Contributors

See the list of [contributors](https://github.com/nooku/nooku-activities/contributors).

## License 

Nooku Activities is free and open-source software licensed under the [GPLv3 license](LICENSE.txt).

## Community

Keep track of development and community news.

* Follow [@joomlatoolsdev on Twitter](https://twitter.com/joomlatoolsdev)
* Join [joomlatools/dev on Gitter](http://gitter.im/joomlatools/dev)
* Read the [Joomlatools Developer Blog](https://www.joomlatools.com/developer/blog/)
* Subscribe to the [Joomlatools Developer Newsletter](https://www.joomlatools.com/developer/newsletter/)
