/**

 @Name: 用户模块

 */
 
layui.define(['laypage', 'fly', 'element'], function(exports){

  var $ = layui.jquery;
  var layer = layui.layer;
  var util = layui.util;
  var laytpl = layui.laytpl;
  var form = layui.form;
  var laypage = layui.laypage;
  var fly = layui.fly;
  var element = layui.element();

  var gather = {}, dom = {
    mine: $('#LAY_mine')
    ,mineview: $('.mine-view')
    ,minemsg: $('#LAY_minemsg')
    ,infobtn: $('#LAY_btninfo')
  };

  //我的相关数据
  var elemUC = $('#LAY_uc'), elemUCM = $('#LAY_ucm');
  gather.minelog = {};
  gather.mine = function(index, type, url){
    var tpl = [
      //求解
      '{{# for(var i = 0; i < d.rows.length; i++){ }}\
      <li>\
        {{# if(d.rows[i].collection_time){ }}\
          <a class="jie-title" href="/jie/{{d.rows[i].id}}.html" target="_blank">{{= d.rows[i].title}}</a>\
          <i>收藏于{{ d.rows[i].collection_time }}</i>\
        {{# } else { }}\
          {{# if(d.rows[i].status == 1){ }}\
          <span class="fly-jing">精</span>\
          {{# } }}\
          {{# if(d.rows[i].accept >= 0){ }}\
          <span class="jie-status jie-status-ok">已解决</span>\
          {{# } }}\
          <a class="jie-title" href="/jie/{{d.rows[i].id}}.html" target="_blank">{{= d.rows[i].title}}</a>\
          <i>{{new Date(d.rows[i].time).toLocaleString()}}</i>\
          {{# if(d.rows[i].accept == -1){ }}\
          <a class="mine-edit" href="/jie/edit/{{d.rows[i].id}}">编辑</a>\
          {{# } }}\
          <em>{{d.rows[i].hits}}阅/{{d.rows[i].comment}}答</em>\
        {{# } }}\
      </li>\
      {{# } }}'
    ];

    var view = function(res){
      var html = laytpl(tpl[0]).render(res);
      dom.mine.children().eq(index).find('span').html(res.count);
      elemUCM.children().eq(index).find('ul').html(res.rows.length === 0 ? '<div class="fly-msg">没有相关数据</div>' : html);
    };

    var page = function(now){
      var curr = now || 1;
      if(gather.minelog[type + '-page-' + curr]){
        view(gather.minelog[type + '-page-' + curr]);
      } else {
        //我收藏的帖
        if(type === 'collection'){
          var nums = 10; //每页出现的数据量
          fly.json(url, {}, function(res){
            res.count = res.rows.length;

            var rows = fly.sort(res.rows, 'collection_timestamp')
            ,render = function(curr){
              var data = []
              ,start = curr*nums - nums
              ,last = start + nums - 1;

              if(last >= rows.length){
                last = curr > 1 ? rows.length - (last - rows.length) : rows.length - 1;
              }

              for(var i = start; i <= last; i++){
                data.push(rows[i]);
              }

              res.rows = data;
              
              view(res);
            };

            render(curr)
            gather.minelog['collect-page-' + curr] = res;

            now || laypage({
              cont: 'LAY_page1'
              ,pages: Math.ceil(rows.length/nums) //得到总页数
              ,skin: 'fly'
              ,curr: curr
              ,jump: function(e, first){
                if(!first){
                  render(e.curr);
                }
              }
            });
          });
        } else {
          fly.json('/api/'+ type +'/', {
            page: curr
          }, function(res){
            view(res);
            gather.minelog['mine-jie-page-' + curr] = res;
            now || laypage({
              cont: 'LAY_page'
              ,pages: res.pages
              ,skin: 'fly'
              ,curr: curr
              ,jump: function(e, first){
                if(!first){
                  page(e.curr);
                }
              }
            });
          });
        }
      }
    };

    if(!gather.minelog[type]){
      page();
    }
  };

  if(elemUC[0]){
    layui.each(dom.mine.children(), function(index, item){
      var othis = $(item)
      gather.mine(index, othis.data('type'), othis.data('url'));
    });
  }

  //Hash地址的定位
  var layid = location.hash.replace(/^#/, '');
  element.tabChange('user', layid);
  
  element.on('tab(user)', function(elem){
    location.hash = ''+ $(this).attr('lay-id');
  });
  
  
  //根据ip获取城市
  if($('#LAY_city').val() === ''){
    $.getScript('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js', function(){
      $('#LAY_city').val(remote_ip_info.city);
    });
  }

  //上传图片
  if($('.upload-img')[0]){
    layui.use('upload', function(upload){
      var avatarAdd = $('.avatar-add');
      layui.upload({
        elem: '.upload-img input'
        ,method: 'post'
        ,url: '/user/upload/'
        ,before: function(){
          avatarAdd.find('.loading').show();
        }
        ,success: function(res){
          if(res.status == 0){
            $.post('/user/set/', {
              avatar: res.url
            }, function(res){
              location.reload();
            });
          } else {
            layer.msg(res.msg, {icon: 5});
          }
          avatarAdd.find('.loading').hide();
        }
        ,error: function(){
          avatarAdd.find('.loading').hide();
        }
      });
    });
  }

  //提交成功后刷新
  fly.form['set-mine'] = function(data, required){
    layer.msg('修改成功', {
      icon: 1
      ,time: 1000
      ,shade: 0.1
    }, function(){
      location.reload();
    });
  }

  //帐号绑定
  $('.acc-unbind').on('click', function(){
    var othis = $(this), type = othis.attr('type');
    layer.confirm('整的要解绑'+ ({
      qq_id: 'QQ'
      ,weibo_id: '微博'
    })[type] + '吗？', {icon: 5}, function(){
      fly.json('/api/unbind', {
        type: type
      }, function(res){
        if(res.status === 0){
          layer.alert('已成功解绑。', {
            icon: 1
            ,end: function(){
              location.reload();
            }
          });
        } else {
          layer.msg(res.msg);
        }
      });
    });
  });


  //我的消息
  gather.minemsg = function(){
    var delAll = $('#LAY_delallmsg')
    ,tpl = '{{# var len = d.rows.length;\
    if(len === 0){ }}\
      <div class="fly-none">您暂时没有最新消息</div>\
    {{# } else { }}\
      <ul class="mine-msg">\
      {{# for(var i = 0; i < len; i++){ }}\
        <li data-id="{{d.rows[i].id}}">\
          <blockquote class="layui-elem-quote">{{ d.rows[i].content}}</blockquote>\
          <p><span>{{d.rows[i].time}}</span><a href="javascript:;" class="layui-btn layui-btn-small layui-btn-danger fly-delete">删除</a></p>\
        </li>\
      {{# } }}\
      </ul>\
    {{# } }}'
    ,delEnd = function(clear){
      if(clear || dom.minemsg.find('.mine-msg li').length === 0){
        dom.minemsg.html('<div class="fly-none">您暂时没有最新消息</div>');
      }
    }
    
    
    // fly.json('/message/find/', {}, function(res){
      // var html = laytpl(tpl).render(res);
      // dom.minemsg.html(html);
      // if(res.rows.length > 0){
        // delAll.removeClass('layui-hide');
      // }
    // });
    
    //阅读后删除
    dom.minemsg.on('click', '.mine-msg li .fly-delete', function(){
      var othis = $(this).parents('li'), id = othis.data('id');
      layer.confirm('确定删除该消息吗？', function(index){
        fly.json('/message/remove/', {
          id: id
        }, function(res){
          if(res.status === 0){
            othis.remove();
            delEnd();
          }
        });
      });
    });

    //删除全部
    $('#LAY_delallmsg').on('click', function(){
      var othis = $(this);
      layer.confirm('确定清空吗？', function(index){
        fly.json('/message/remove/', {
          all: true
        }, function(res){
          if(res.status === 0){
            layer.close(index);
            othis.addClass('layui-hide');
            delEnd(true);
          }
        });
      });
    });

  };

  dom.minemsg[0] && gather.minemsg();

  exports('user', null);
  
});