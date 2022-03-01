Kohana 3.2 Pagination
---

This is pretty much hacked up to support Kohana 3.2 so there are a few things to note:

- Request, Route and route parameters dependency injection added
- Current Request is used by default instead of the initial one ($_GET was used directly in < 3.2)
- URL::query() has been removed, Pagination::query() added instead (HMVC support)