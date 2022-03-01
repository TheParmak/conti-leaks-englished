All requests are filtering, and can be accessed only from localhost.
------------------------------------------------------------------------------
# 1. get init list with BlackList

uri: https://localhost:444/sendmail/dnsbl

e.g.:
[
    "MTg4LjcyLjEyNS4xNDAJNgkx",
    "MTcyLjExMS4xMjkuMTMyCTYJMQ==",
    ...
]
------------------------------------------------------------------------------
# 2. get init list with WhiteList clientID's

uri: https://localhost:444/sendmail/whitelist

e.g.:
[
    "MTg4LjcyLjEyNS4xNDAJNgkx",
    "MTcyLjExMS4xMjkuMTMyCTYJMQ==",
    ...
]
------------------------------------------------------------------------------