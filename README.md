![Statamic 3.0+](https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge&link=https://statamic.com)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/f2fa45074e8242ee97c2dcaa0f568fd6)](https://www.codacy.com/manual/jcohlmeyer/two-fa?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=kind-work/two-fa&amp;utm_campaign=Badge_Grade)

## Two Factor Login for Statamic 3

Statamic 2FA is a middleware addon for [Statamic 3](https://github.com/statamic/cms) that adds 2FA (2 factor) auth to the control panel of Statamic 3 using time based codes.

## Pricing

Statamic 2FA is commercial software. You do not need a licence for development but when you are ready to deploy the site to production please purchase a licence per site on the [Statamic Marketplace](https://statamic.com/marketplace/addons/2fa).

## Install

### Install the addon using composer

```composer require kind-work/two-fa```

## Usage

Add the `two_fa` field to your user blueprint. Edit your user profile in the control panel (CP) to set up 2FA protection for your account.

```yaml
title: User
sections:
  main:
    display: Main
    fields:
      -
        handle: name
        field:
          type: text
          display: Name
      -
        handle: email
        field:
          type: text
          input: email
          display: 'Email Address'
      -
        handle: roles
        field:
          type: user_roles
          width: 50
      -
        handle: groups
        field:
          type: user_groups
          width: 50
      -
        handle: avatar
        field:
          type: assets
          max_files: 1
      -
        handle: two_fa
        field:
          type: two_fa
          localizable: false
          display: 'Two FA'
```
