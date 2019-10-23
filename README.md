# About the project

This project shows 2 things: [JSON RPC 2.0](https://www.jsonrpc.org/specification) implementation on Symfony and data validation and deserialization without [Symfony Forms](https://symfony.com/doc/current/forms.html).

## JSON RPC implementation details

This project fully implements specification except of batch requests. Because of there are no elegant ways to do async calls in PHP so batch requests are no so useful here: every request in batch will be called in sequence which will be very slow for big batch requests.

It's recommended to create reverse proxy between web server and Symfony application on any language that supports async calls (such as JavaScript) with batch requests implementation.

## What's wrong with Symfony forms

Symfony Forms are not designed to validate JSON: they were created to manage HTML forms only. So you, for example, can't send `true` or `false` to Symfony Forms because there are no booleans in HTML. You may think that `<input type="checkbox">` is kinda boolean, but no: if checkbox is checked, then value `checkboxname=on` will be sent to server, but if checkbox is unchecked, then field will not be sent at all! So if your Symfony's boolean field has `NotBlank` or `NotNull` constraint, you will not be able to send `false` because it will be converted to `null` and it will trigger these constraints. This project shows how to use Symfony Serializer for validation and deserialization on simple JSON RPC method called `sum` which accepts 2 integers (`a` and `b`) and returns their sum.

## Error messages

Traditional APIs also return errors as strings like "Your password is too short", but:

1. API should care about errors messages: they should be user-friendly and vary universal (cuz clients will display them on front-end, mobile apps and every where). But we don't want to know about who works with out API: we just want return simple, but informative errors for developers such as "String is too long".
2. API should care about translations. When your app will receive new language, everybody will should to translate entire app (even entire API app). But in perfect world our backend app should not have any translations at all (except of SMSs, emails and other things which may be moved to micro services).

That applies a lot of limitations to API developers. How can we avoid these problems? Using error IDs instead of error messages! Instead of using string like "Your password is too short" we can return following JSON:

```json
{
  "type": "string_is_too_short",
  "parameters": [{
    "name": "minimalLength",
    "value": 8
  }]
}
```

And then client application will be able to create any error message on any language for any context just by identifying error type: `Value is too short. It should be {{ minimalLength }} characters length`.
