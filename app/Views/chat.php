<div class="container">
  <div class="row">
    <div class="col-12 mt-5 pt-3 pb-3 bg-white from-wrapper">
      <div class="container">
        <h3>Chat</h3>
        <hr>
        <div class="row">
          <div class="col-12 col-sm-12 col-md-4 mb-3">
            <ul id="user-list" class="list-group"></ul>
          </div>
          <div class="col-12 col-sm-12 col-md-8">
            <div class="row">
              <div class="col-12">
                <div class="message-holder">
                    <div id="messages" class="row"></div>
                </div>
                <div class="form-group">
                 <textarea id="message-input" class="form-control" name="" rows="2"></textarea>
                </div>
            </div>
              <div class="col-12">
                <button id="send" class="btn float-right  btn-primary">Send</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
         $(function () {
            scrollMsgBottom()
        })
        
        function scrollMsgBottom(){
          var d = $('.message-holder');
                    d.scrollTop(d.prop("scrollHeight"));
        }

        function getImages(){
          const imgs = {
            'Mary' : 'mary.jpg',
            'Jon' : 'jon.jpg',
            'Alex' : 'alex.jpg',
          }

          return imgs
        }

        $(function () {
            var conn = new WebSocket('ws://localhost:8080?access_token=<?= session()->get('id') ?>');
            conn.onopen = function(e) {
                console.log("Connection established!");
            };

            conn.onmessage = function(e) {
              console.log(e.data);
              
              var data = JSON.parse(e.data)
              
              if ('users' in data){
                updateUsers(data.users)
              } else if('message' in data){
                newMessage(data)
              }

            };

            $('#send').on('click', function () {
                var msg = $('#message-input').val()
                if(msg.trim() == '')
                  return false
                conn.send(msg);
                myMessage(msg)
                $('#message-input').val('')
            })
        })

        function newMessage(msg){
          const imgs = getImages()
          
          html = `<div class="col-8 msg-item left-msg">
                    <div class="msg-img">
                      <img class="img-thumbnail rounded-circle" src="/assets/img/` + imgs[msg.author] + `">
                    </div>
                    <div class="msg-text">
                      <span class="author">` + msg.author + `</span> <span class="time">` + msg.time + `</span><br>
                      <p>` + msg.message + `</p>
                    </div>
                  </div>`
          $('#messages').append(html)
          scrollMsgBottom()
        
        }

        function myMessage(msg){
          var name = '<?= session()->get('firstname') ?>'
          const imgs = getImages()
          var date = new Date;
          var minutes = date.getMinutes();
          var hour = date.getHours();
          var time = hour + ':' + minutes
          html = `<div class="col-8 msg-item right-msg offset-4">
                    <div class="msg-img">
                      <img class="img-thumbnail rounded-circle" src="/assets/img/` + imgs[name] + `">
                    </div>
                    <div class="msg-text">
                      <span class="author">Me</span> <span class="time">` + time + `</span><br>
                      <p>` + msg + `</p>
                    </div>
                  </div>`
          $('#messages').append(html)
          scrollMsgBottom()
        }

        function updateUsers(users){
          var html = ''
          var myId = <?= session()->get('id') ?>;
          
          
          for (let index = 0; index < users.length; index++) {
            if(myId != users[index].c_user_id)
            html += '<li class="list-group-item">'+ users[index].c_name +'</li>'
          }

          if(html == ''){
            html = '<p>The Chat Room is Empty</p>'
          }
          

          $('#user-list').html(html)
          

        }
    </script>