<center>
  <div class="column">
    <!--  -->
    <h2 class="ui header noprint">
      <i class="ui sort amount down icon massive"></i>
      <div class="content">
        Top Performers List
        <div class="sub header">List Section/Department Top Performers</div>
      </div>
    </h2>
    <!--  -->
  </div>
</center>

<div class="ui basic segment" id="topPerformers-app">
  <button class="ui button" @click="backPage()"><i class="ui chevron left icon"></i> Back</button>
  <table class="ui structured celled  selectable striped table">
    <thead>
      <tr>
        <th width="5">#</th>
        <th width="500">Name</th>
        <!-- <th>Performance Rating</th> -->
        <th class="center aligned">Performance Rating</th>
        <th class="center aligned">Performance Quality</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item, i in list" :key="item.id">
        <td>{{ i+1 }}</td>
        <td> {{ item.full_name }} </td>
        <!-- <td> {{ item.final_numerical_rating }} </td> -->
        <td class="center aligned"> {{ item.final_numerical_rating_recomp }} </td>
        <td class="center aligned">{{ item.final_numerical_rating_recomp_scale }}</td>
      </tr>
    </tbody>
  </table>
</div>
<!-- <script src="topPerformers.js"></script> -->
<script>
  // $(document).ready(showRev("viewTopPerformers"));
  const {
    createApp
  } = Vue

  createApp({
    data() {
      return {
        list: []
      }
    },
    methods: {
      backPage() {
        history.back()
      },
      getList() {
        $.post('?config=topPerformers', {
          getList: true,
        }, (data, textStatus, xhr) => {
          console.log("getList: ", data);
          this.list = JSON.parse(data)
          $("#appLoader").dimmer("hide");
        });
      },
    },
    mounted() {
      $("#appLoader").dimmer({ closable: false }).dimmer("show");
      this.getList()
    },

  }).mount('#topPerformers-app')
</script>