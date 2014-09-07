# h4cc/mongrel2

A Handler library for Mongrel2 and the according ZeroMQ message protocol.

This library is capable of reading requests and writing responses in mongrel2 message format over zeromq.
The resulting data objects are `Request` and `Response`, which are modelled after the PHP SAPI with its `$_GET, $_POST, $_SERVER, ...` arrays.

## Usage 

Have a look at `example/handler.php`.

## Current State

The mapping of requests is quite complete, except the handling of file uploads and populating the `$_FILES` array due to a missing `multipart/*` parser.

Also more complex scenarios like async-file-uploads or websockets are not yet handled.

Any help in improving this library is appreciated :)

