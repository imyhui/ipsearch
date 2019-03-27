@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="/search">
                <div class="input-group">
                    <input type="text" class="form-control h50" name="ip" placeholder="ip..." value="{{ $ip }}">
                    <span class="input-group-btn"><button class="btn btn-default h50" type="submit" type="button"><span
                                    class="glyphicon glyphicon-search"></span></button></span>
                </div>
            </form>
        </div>
    </div>
    @if($ip)
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default list-panel search-results">
                    <div class="panel-heading">
                        <h3 class="panel-title ">
                            <i class="fa fa-search"></i> ip为 “<span class="highlight">{{ $ip }}</span>” 的搜索结果
                        </h3>
                    </div>

                    <div class="panel-body ">
                        <h2 class="title">
                            <a href="{{ $ip }}" target="_blank">
                                {{ $ip }}
                            </a>
                        </h2>
                        <div class="info">
                        </div>
                        <div class="desc">
                            <pre id='show_p'>{!! $ipData !!}</pre>

                            <script type="text/javascript">
                                var text = document.getElementById('show_p').innerText; //获取json格式内容
                                var result = JSON.stringify(JSON.parse(text), null, 2);//将字符串转换成json对象
                                document.getElementById('show_p').innerText = result;
                            </script>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row text-center">
            <div class="col-md-12">
                <br>
                <h2>Search IP by Click！</h2>
                <br>
                <p>网络空间探测器</p>
            </div>
        </div>
    @endif
@endsection