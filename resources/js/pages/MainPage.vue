<template>
  <div class="container">
    <div class="main__form">
      <form>
        <input type="hidden" name="_token" :value="csrf">
        <div class="form-group mt-2">       
           <label for="name" class="mt-2">Введите город:</label>
           <input type="text" name="name" class="form-control mt-2" placeholder="Город" id="name" v-model="city">
        </div>          
      </form>
    </div>
    <div v-if="getPage==true">
      <test-component :city="city" :keyCSR="csrf" @checkCity="isRulesReaded=$event"></test-component>  
    </div>
    <div v-else>
     <history :city="city" :keyCSR="csrf"></history>
    </div>
    <div class="more">
      <button type="button" class="btn btn-success" @click="getPage=!getPage" :disabled="!isRulesReaded">
        {{ getPage? 'Перейти на страницу с историей' : 'Перейти на страницу с текущей погодой' }}
      </button>
    </div>
  </div>
</template>

<script>

import TestComponent from '../components/TestComponent';
import history from '../components/history';


export default {
  data(){
    return{
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      city:'',
      getPage:true,
      isRulesReaded: false
    }
  },
  components: { 
    TestComponent,
    history
   }
}
</script>

<style scoped lang="sass">
.btn-success
  font-weight: 650
  border: 2px solid #e8e8e8
.more 
  position: absolute
  bottom: 20px
  left: 50%
  transform: translateX(-50%)
.main__form
  label
    color: white

</style>