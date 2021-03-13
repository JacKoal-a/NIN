# NIN PHP

## Neural Improper Network

REST API to create, train and use neural networks

## How To Use

1. Create a Model

2. Create his Classes

3. Add the training images of each Class,  you can also use the following [interface](/nin/addimage.html)

4. Train the Model

5. Use the Model

## Endpoints

### **Get Model**

Retrive model's informations

```http
GET    /nin/api/model/get.php?name={model_id}
```

Parameters

> **name**
> 
>     *desciption*:    ID of desidered model
> 
>     *type*:    string

Response

```json
{
    "result" : [
        {
            "name": {model_id},
            "description": {model_description},
            "created": {model_creation_timestamp},
            "last_train": {model_last_train_timestamp},
            "classes": [
                {
                    "id": {class_id},
                    "name": {class_name},
                    "description": {class_desc}
                },
                {...},
                ...
            ]
        }
    ]
}
```

### **Get Models**

Retrive all models' informations

```http
GET    /nin/api/model/get.php
```

Response

```json
{
    "result" : [
        {
            "name": {model_id},
            "description": {model_description},
            "created": {model_creation_timestamp},
            "last_train": {model_last_train_timestamp},
            "classes": [
                {
                    "id": {class_id},
                    "name": {class_name},
                    "description": {class_desc}
                },
                {...},
                ...
            ]
        },
        {...},
        ...
    ]
}
```

### **Create Model**

```http
POST    /nin/api/model/create.php
```

Body Request

*Content-Type:    application/json*

```json
{
    "name":{name},
    "desc":{description}
}
```

Parameters

> **name**
> 
>    *description*:    Model name, it will also be its ID
> 
>     *type*:    string

> **desc**
> 
>     *descriptio*n:    Short model descriptiom
> 
>     *type*:    string

Response 

```json
{
    "model_id": {model_id},
    "message": {status_msg},
    "endpoints": {
        "create_class": "/nin/api/class/create.php",
        "train": "/nin/api/model/train.php?model={model_id}",
        "respond": "/nin/api/model/respond.php?model={model_id}"
    }
}
```

### **Train Model**

Train the model on his current classes data 

```http
PATCH    /nin/api/model/train.php?model={model_id}
```

Params

> **model**
> 
>     *desciption*:    ID of desidered model
> 
>     *type*:    string

Response

```json
{
    "model_id": {model_id},
    "message": {status_msg},
    "endpoints": {
        "respond": "/nin/api/model/respond.php?model={model_id}"
    }
}
```

#### 

### **Respond Model**

Retrive model result processing a given image, it prints out all classes and their score in descending order of score

```http
POST    /nin/api/model/respond.php?model={model_id}
```

Body Request

*Content-Type:    multipart/form-data*

```json
  KEY      TYPE       VALUE

"image"    file    {image_file}
```

Params

> **model**
> 
>     *desciption*:    ID of desidered model
> 
>     *type*:    string

> **image**
> 
>     *desciption*:     Image to process
> 
>     *type*:    image/jpeg ,  image/png

Response

```json
{
    "message": {status_msg},
    "result": [
        {
            "class": {class_name},
            "score": {score}
        },
        {...},
        ...
    ]
}
```

### **Create Class**

```http
POST    /nin/api/model/create.php
```

Body Request

*Content-Type:    application/json*

```json
{
    "name":{name},
    "desc":{description},
    "model":{model_id}
}
```

Parameters

> **name**
> 
>     *description*:    Class name
> 
>     *type*:    string

> **desc**
> 
>     *description*:    Short class description
> 
>     *type*:    string

> **model** 
> 
>     *description*:    ID of desidered model
> 
>     *type*:    string

Response

```json
{
    "class_id": {class_id},
    "message": {status_msg},
    "endpoints": {
        "add_image": "/nin/api/class/add_image.php"
    }
}
```

### Add Image

Add a training image to a class

```http
POST    /nin/api/model/create.php
```

Body Request

*Content-Type:    multipart/form-data*

```json
KEY          TYPE        VALUE
"image"      file        {image_file}
"class_id"   string      {class_id}
```

Params

> **class_id**
> 
>     *desciption*:    ID of desidered class
> 
>     *type*:    string

> **image**
> 
>     *desciption*:     Image to add
> 
>     *type*:    image/jpeg , image/png

Response

```json
{
    "message": {status_msg},
    "result": [
        {
            "class": {class_name},
            "score": {score}
        },
        {...},
        ...
    ]
}
```

### Get Class

Retrive class's informations

```http
GET    /nin/api/class/get.php?name={class_id}
```

Params

> **name**
>     *desciption*:    ID of desidered class
> 
>     *type*:    string

Response

```json
{
    "result" : [
        {
            "id": {class_id},
            "name": {class_name},
            "description": {class_description},
            "model_id": {model_id}
        }
    ]
}
```

### **Get Classes**

Retrive all classes's informations

```http
GET    /nin/api/class/get.php
```

Response

```json
{
    "result" : [
        {
            "id": {class_id},
            "name": {class_name},
            "description": {class_description},
            "model_id": {model_id}
        },
        {...},
        ...
    ]
}
```
