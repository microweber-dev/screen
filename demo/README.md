#Demo

This is just a demonstration of how you can use this library

## index.html

Here you can find a basic form that requests the `shot.php` file to screenshot the websites.

## shot.php

You can directly render the taken screen-shot with the `shot.php` file

You can render any link by passing it as `url` parameter: `shot.php?url=google.com`

You can specify height and width: `shot.php?url=google.com&w=300&h=100`

If you want to crop/clip the screen shot, you can do so like this: `shot.php?url=google.com&w=800&h=600&clipw=800&cliph=600`

You can also set a user agent string to be used on the request: `shot.php?url=google.com&user-agent=some-random-user-agent-string`

**Note**: You should `urlencode` the values in case there is any space or tricky character.

## clean-jobs.php

Here you can se an example on how to delete the job files that are generated automatically.
