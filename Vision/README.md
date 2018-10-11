> ```
>  __                         __  ___    __       
> |  \  /\  | |    \ /  |\/| /  \  |  | /  \ |\ | 
> |__/ /~~\ | |___  |   |  | \__/  |  | \__/ | \| 
>                                                                                          
> ___  ___  __  ___                       
>  |  |__  /__`  |                        
>  |  |___ .__/  |                        
>
> ```                                       

## Introduction

Dailymotion is building a new feature "The playlist"
The feature is simple : The user can create a list of ordered videos.
As a core api developer, you are responsible for building this feature and expose it through API.

Task
The task is to create an api that manages an ordered playlist.
An example of a minimal video model : (You might add extra fields to do this project)

<pre><code>
video {
    id : the id of the video,
    title: the title of the video
    thumbnail : The url of the video
    ...
}
</code></pre>

An example of a minimal playlist model : (You might add extra fields to do this project)

<pre><code>
playlist {
    id : The id of the playlist,
    name : The name of the playlist
    ......
}
</code></pre>

The API must support the following use cases:

- Return the list of all videos:

<pre><code>
{
    "data" : [
        {
            "id": 1,
            "title": "video 1"
            ....
        },
        {
            "id": 2,
            "title": "video 2"
        }
        ....
        ]
}
</code></pre>

- Return the list of all playlists:

<pre><code>
{
    "data" : [
        {
            "id": 1,
            "name": "playlist 1"
            ....
        },
        {
            "id": 2,
            "name": "playlist 2"
        }
        ….

    ]
}
</code></pre>

- Create a playlist

- Show informations about the playlist

<pre><code>
{
    "data" : {
        "id": 1,
        "name": "playlist 1"
    }
}
</code></pre>

- Update informations about the playlist
- Delete the playlist
- Add a video in a playlist
- Delete a video from a playlist
- Return the list of all videos from a playlist (ordered by position):

<pre><code>
{
    "data" : [
        {
            "id": 1,
            "title": "video 1 from playlist 2"
            ....
        },
        {
            "id": 2,
            "title": "video 2 from playlist 2"
        }
        ....
    ]
}
</code></pre>

## Rules

Your goal: Design and build this API.

Important notes :
- Removing videos should re-arrange the order of your playlist and the storage.
- PHP or Python languages are supported
- Using frameworks is forbidden, your code should use native language libraries, except for Python, you could use bottlepy (https://bottlepy.org/docs/dev/).
- Use Mysql for storing your data
- You should provide us the source code (or a link to GitHub) and the instructions to run your code

## Setting

- Enable mod_rewrite on tour apache server.
- Configure your VirtualHost and .htaccess with your path.

## Action

### GET

/playlists/                    Get all playlists
/playlists/id                  Get a playlist 
/videos/                       Get all videos
/videos/id                     Get a video          
/playlists/id/videos           Get all videos of a playlist            
/videos/id/playlists           Get all playlists of a video     

### POST

/playlists/                    Add a playlist    
/videos/                       Add a video   

### PUT

/playlists/id                  Update a playlist    
/videos/id                     Update a video   
/playlists/id/videos/id        Add a video to a playlist  
  
### DELETE

/playlists/id                  Delete a playlist    
/videos/id                     Delete a video
/playlists/id/videos/id        Delete a video to a playlist 

## Schema

view postman test 
