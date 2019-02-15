<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('bind') }}">
        <input type="hidden" name="userId" value="{{ $userId }}">
        <div class="form-group">
            <label for="studentId">學號</label>
            <input type="text" class="form-control" id="studentId" name="studentId" placeholder="請輸入菁英學號">
        </div>
        <div class="form-group">
            <label for="studentPassword">密碼</label>
            <input type="password" class="form-control" id="studentPassword" placeholder="請輸入密碼" name="password">
        </div>
        <button type="submit" class="btn btn-primary">送出</button>
    </form>
</body>
</html>