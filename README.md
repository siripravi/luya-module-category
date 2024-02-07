# LUYA module for Category Administration

<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# LUYA _siripravi/category_

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)

_Luya module for nested set administration_

## Requirement

This module requires

[Yii2 Nestedset Extension] (https://github.com/creocoder/yii2-nested-sets/)

## Installation

```sh
composer require creocoder/yii2-nested-sets
Install the extension through composer:
```

```sh
composer require siripravi/luya-module-category
```

Add the following to your config module listing:

```
 'categoryadmin' => 'siripravi\category\admin\Module',
```

Run these two commands afterwards:

```sh
./vendor/bin/luya import
```

```sh
./vendor/bin/luya migrate
```

## Module Features

1. Support for multiple tree creation.
2. Insert/update/modify a new tree node anywhere in the tree.
3. Deletion possible for multiple nodes at a time.

:laughing:
