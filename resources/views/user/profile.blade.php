@extends('layouts.app')
@section('title')
{{ $user->name }} <br/>
{{ $user->email }}
@endsection
@section('content')
<div>
  <ul class="list-group">
    <li class="list-group-item">
      Joined on {{$user->created_at->format('M d,Y \a\t h:i a') }}
    </li>
    <li class="list-group-item panel-body">
      <table class="table-padding">
        <style>
          .table-padding td{
            padding: 3px 8px;
          }
        </style>
        <tr>
          <td>Total Posts</td>
          <td> {{$posts_count}}</td>
          @if($author && $posts_count)
          <td><a href="{{ url('/my-all-posts')}}">show all</a></td>
          @endif
        </tr>
        <tr>
          <td>Posted Posts</td>
          <td>{{$posts_active_count}}</td>
          @if($posts_active_count)
          <td><a href="{{ url('/user/'.$user->id.'/posts')}}">show all</a></td>
          @endif
        </tr>
        <tr>
          <td>Posts in Drafts</td>
          <td>{{$posts_draft_count}}</td>
          @if($author && $posts_draft_count)
          <td><a href="{{ url('my-drafts')}}">show all</a></td>
          @endif
        </tr>
      </table>
    </li>
    <li class="list-group-item">
      Total comments {{$comments_count}}
    </li>
  </ul>
</div>
<div class="panel panel-default">
  <div class="panel-heading"><h3>Recent Posts</h3></div>
  <div class="panel-body">
    @if(!empty($latest_posts[0]))
    @foreach($latest_posts as $latest_post)
      <p>
        <strong><a href="{{ url('/'.$latest_post->slug) }}">{{ $latest_post->title }}</a></strong>
        <span class="well-sm">От {{ $latest_post->created_at->format('M d,Y \a\t h:i a') }}</span>
      </p>
    @endforeach
    @else
    <p>You have no posts yet.</p>
    @endif
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading"><h3>latest comments</h3></div>
  <div class="list-group">
    @if(!empty($latest_comments[0]))
    @foreach($latest_comments as $latest_comment)
      <div class="list-group-item">
        <p>{{ $latest_comment->body }}</p>
        <p>On {{ $latest_comment->created_at->format('M d,Y \a\t h:i a') }}</p>
        <p>On post <a href="{{ url('/'.$latest_comment->post->slug) }}">{{ $latest_comment->post->title }}</a></p>
      </div>
    @endforeach
    @else
    <div class="list-group-item">
      <p>You have no comments. Your last 5 comments will be displayed here.</p>
    </div>
    @endif
  </div>
</div>
@endsection