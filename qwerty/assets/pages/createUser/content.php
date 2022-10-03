<div class="container">
    <div id="showLogs"></div>
</div>

<script type="text/javascript">
    (function(){
        'use strict';
        var logs = "";
        let uncreatedAcc = "";
        logs += document.readyState+"</br>";
        var showLog = setInterval(function(){
            sl();
        }, 200);    
        var c = setInterval(function(){
                var rstate = document.readyState;
                if(rstate=="complete"){
                    logs += rstate+"</br>";
                    clearInterval(c);
                    get_employees();
                }            
        }, 1000);    

        function get_employees(){
            logs += "Starting to fetch employees without accounts.......<br>";
            let dat = ""; 
            var fd = new FormData();
                fd.append('getEmp',true);
            var xhr = new XMLHttpRequest();
                xhr.onreadystatechange  = function(){
                    if (this.readyState === this.DONE) {
                        uncreatedAcc = JSON.parse(xhr.responseText);
                        logs += "Data Fetched: "+uncreatedAcc.length+" total uncreated accounts</br>";
                        createAccount(0);
                    }
                }
                xhr.open('POST','?config=createUser',false);
                xhr.send(fd);
        
        }
        function createAccount(count){
            var l = uncreatedAcc.length;
            if(count<l){
                var emp = uncreatedAcc[count]; 
                let frst = emp['firstName'].split(' ');
                let username = "";
                    var c = 0;
                    while(c<frst.length){
                        username +=frst[c][0];
                        c++;
                    }
                if(emp['middleName'][0]!="."){
                    username +=emp['middleName'][0];
                }
                username +=emp['lastName'];
                username = username.toLowerCase();
                var fd = new FormData();
                fd.append('username',username);
                fd.append('userId', emp['employees_id']);
                fd.append('createAccount',true);
                var xml = new XMLHttpRequest();
                xml.onreadystatechange = function(){
                    if(xml.responseText=="1"||xml.responseText==1){
                        logs += emp['firstName']+" "+emp['middleName']+" "+emp['lastName']+" = "+username+" ID No:"+emp['employees_id']+"<br>";
                        count++;
                        createAccount(count);
                    }else{
                        alert(error)
                    }
                }
                xml.open('POST','?config=createUser',true);
                xml.send(fd);
            }else{
                logs += "DONE!!! :) <br>";
            }
        }
        function _(el){
            return document.getElementById(el);
        }
        function sl(){
            _('showLogs').innerHTML = logs;
        }
    }());
</script>