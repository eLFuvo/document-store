Experimental, on development
--------------

Known issues
--------------

- database (collection) name with dot cause exception when execute simple queries like count (calls native
  database.collection.count())
- inserting of not valid document (validated by index) cause non-informative exceptions like "[HY000] Document is
  missing a required field"
- ~~\mysql_xdevapi\Collection->addOrReplace() brake simple array in document. doc->foo = ['a','b','c'] => {"foo": {"0":"
  a", "1":"b", "2":"c"}}~~ - fixed into mysql_xdevapi 8.0.26
- \mysql_xdevapi\Collection->patch() encodes utf8 symbols and adding unnecessary slashes
- \mysql_xdevapi\Collection->modify()->set('property', json_encoded_string_with_2_more_deep_tree) adding unnecessary
  slashes for special symbols like \r\n\t
- indexed field can't be **NULL**
- maximum fields for filtering query is 100 due exception `[HY000] X Protocol message recursion limit (100).`

TODO:

- DB tests
- creating of schema validation
- refactor code - executing of query must be by one method
- add logging support
