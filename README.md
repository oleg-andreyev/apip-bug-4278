Reproduce bug by "api_platform.graphql.serializer.context_builder" and "api_platform.graphql.resolver.stage.serialize" overrides
use example.http to make requests.

Actual result:
```json
{
  "data": {
    "CreateFromCartSaveForLater": {
      "Cart": null,
      "SaveForLater": null
    }
  }
}
```

Expected result
```json
{
  "data": {
    "CreateFromCartSaveForLater": {
      "Cart": {
        "id": "cb9e231a-b263-11eb-aa22-0242238ca07b",
        "items": null
      },
      "SaveForLater": [
        {
          "id": "\/api\/save_for_laters\/cb9cb26e-b263-11eb-859e-0242238ca07b",
          "title": "foobar"
        }
      ]
    }
  }
}
```