<template>
  <div class="historyweathers__wrapper">
    <div class="row">
      <div class="col-sm">
        <label for="example-datepicker">Выберите дату с которой вы хотите увидеть изменения погоды</label>
        <b-form-datepicker v-model="start" :min="min" :max="max" locale="en"></b-form-datepicker>
      </div>
      <div class="col-sm">
        <label for="example-datepicker">Выберите дату до которой вы хотите увидеть изменения погоды</label>
        <b-form-datepicker v-model="end" :min="min" :max="max" locale="en"></b-form-datepicker>
      </div>
    </div>
    <button type="button" class="btn btn-success mt-2" @click="getChangeWeather">
       {{ scrollTable? 'Спрятать' : 'Показать' }}
    </button>
    <div v-if="errorFlag">
        <b-alert show variant="danger" class=error__block>История за данные даты пуста, либо вы не ввели даты!</b-alert>   
    </div>
    <div v-if="scrollTable" class="table-weather">
        <b-table striped hover :items="items"></b-table>
    </div>
  </div>
</template>

<script>
  export default {
    props:{
        city:{
            type: String
        },
        keyCSR:{
            type: String
        }
        },
    data() {
      return {
        start: '',
        end:'',
        min:'',
        max: '',
        items: [],
        scrollTable: false,
        errorFlag: false
      }
    },
    mounted() {
      this.getTime();
    },            
    methods: {
      async getTime() {
              const response = await axios.get(`/cans?_token=${this.keyCSR}&name=${this.city}`)
              this.min = response.data[0].time_weather;
              const today = new Date();
              const date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
              this.max = date;     
      },
      async getChangeWeather() {
            const response = await axios.get(`/getChange?_token=${this.keyCSR}&name=${this.city}&start=${this.start}&end=${this.end}`);
            this.items = response.data;
            this.scrollTable = !this.scrollTable;
            this.errorFlag = false;
            if (response.data.length == 0){
              this.errorFlag = true;
              this.scrollTable = !this.scrollTable;
            }  
      }  
    }
  }
</script>

<style lang="sass" scoped>
// .historyweathers
//     &__wrapper
//         background-color: blue
.wrapp
  display: flex
  margin-top: 5px

.table-weather
  margin-top: 40px
  min-height: 300px
  max-height: 500px
  overflow-y: scroll
  background-color: white
label
  color: white
  font-weight: 600
.error__block
    margin-top: 30px
    text-align: center
    font-weight: 600
</style>