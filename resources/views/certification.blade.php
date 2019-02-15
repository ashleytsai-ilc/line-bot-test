<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <div class="container">
    <form class="mt-3" action="{{ route('bind') }}" method="POST">
        <input type="hidden" name="userId" value="{{ $userId }}">
        <div class="form-group row">
            <label for="studentId" class="col-sm-1 col-form-label">學號</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="studentId" name="studentId" placeholder="請輸入菁英學號">
            </div>
        </div>
        <div class="form-group row">
            <label for="studentPassword" class="col-sm-1 col-form-label">密碼</label>
            <div class="col-sm-5">
                <input type="password" class="form-control" id="studentPassword" placeholder="請輸入密碼" name="password">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">送出</button>
    </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
</body>
</html>