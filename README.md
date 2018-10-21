# README
Fibonacci Server component

## Installation


## API

### 1. Create a new user
This call creates a new user.


#### How to call
Create a POST call to this URL `/new/user/[user]/`

For example `/create_user/user/gerb030/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Username | String | 20      |
| Email | String | 100      |

#### Returns
For successful requests, the given username is returned:

| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |

In case the username is already taken, the call will fail with a 500 server error message and a descriptive error message.

### 2. Create new round
This call creates a new round.

#### How to call
Create a POST call to this URL `/new/user/[user]/`

For example `/new/user/gerb030/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| User | String | 20      |

#### Returns
For successful requests, a randomly generated Session number is created and returned:

| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |


### 3. Join existing round
The user joins an existing round. This is done on an invitation basis, no additional authentication is required.

#### How to call
Create a POST call to this URL `/join/session/[session]/user/[user]/`

For example `/join/session/12345/user/gerb030/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |
| User | String | 20      |

#### Returns
For successful requests, a 200 OK is returned.

### 4. Place a vote in an existing round
If the user has joined an existing round, The user can vote for an existing round.

#### How to call
Create a POST call to this URL `/vote/session/[session]/user/[user]/vote/[vote]`

For example `/vote/session/12345/user/gerb030/vote/13` or `/vote/session/12345/user/gerb030/vote/☕️`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |
| User | String | 20      |
| Vote | String | 1      |

#### Returns
For successful requests, a 200 OK is returned.


### 5. Poll vote completion
Check which votes are already in

#### How to call
Create a GET call to this URL `/poll/session/[session]/`. This call is commonly made by the web front-end.

For example `/poll/session/12345/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |

#### Returns
For successful requests, a 200 OK is returned with the following JSON response:
`
{
	"session" : "[session_id]",
	"votes" : [
		{"gerb030" : null},
		{"gerb020" : "☕️"},
		{"gerb010" : "3"}
	]
}
`
### 6. Reset round
The owner of a user is able to start a new round by resetting the votes.

#### How to call
Create a POST call to this URL `/restart/session/[session]/`

For example `/restart/session/12345/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |

#### Returns
For successful requests, a 200 OK is returned.



### 7. Kick an existing user from a round
The owner is able to kick a joined user from a round.

#### How to call
Create a POST call to this URL `/kick/session/[session]/user/[user]/`

For example `/kick/session/12345/user/gerb030/`

#### Parameters
| Name       | Type    | Length |
|------------|---------|--------|
| Session | Integer | 5      |
| User | String | 20      |
| Vote | String | 1      |

#### Returns
For successful requests, a 200 OK is returned.


### Errors
The following error codes apply to all responses:

| HTTP       | Description |
|------------|---------|
| 401 | User not in round |
| 404 | Round or user not found |
| 500 | General server error |


