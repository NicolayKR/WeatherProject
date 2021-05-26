<template>
    <div>
        <form>          
            <button type="button" class="btn btn-success mt-2" @click="postCity">
                 {{ flagButton? 'Спрятать' : 'Показать' }}
            </button>
        </form>        
        <div v-if="errorFlag">
            <b-alert show variant="danger" class=error__block>Неправильное название города!</b-alert>   
        </div>
        <div id="weather" v-if="flagButton">
            <ul class="catalog__wrapper">
                <li v-for="cityWeather in cityWeathers" :key="cityWeather">
                    <div class="catalog-item">
                        <div class="catalog-item__wrapper">
                            <div class="catalog-item__content">
                                <img :src="cityWeather.icon" alt="" class="catalog-item__img">
                                <div class="catalog-item__subtitle">{{cityWeather.name}}</div>
                                <div class="catalog-item__descr">
                                    {{getWeekDayName(cityWeather.date)}}
                                    <br> Ожидаемая погода:
                                </div>
                                <ul class="catalog-item__list">
                                    <li>Ожидается : {{cityWeather.curr_temperat}}°C</li>
                                    <li>{{cityWeather.description}}</li>
                                    <li>Средняя температура сегодня : {{cityWeather.avg_temp}}°C</li>
                                    <li>Атмосферное давление : {{cityWeather.pressure}}</li>
                                    <li>Скорость ветра : {{cityWeather.wind}}'m/s'</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
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
        data()  {
            return {
                flagButton: false,
                dayWeek:[],
                cityWeathers:[],
                errorFlag: false
            }
        },
        // mounted(){
        //     this.update();
        // },
        methods:{      
            async postCity() {
                try{
                const response = await axios.get(`/can?_token=${this.keyCSR}&name=${this.city}`)  
                    console.log(response);
                    this.cityWeathers = response.data;
                    console.log(this.cityWeathers);
                    this.flagButton= !this.flagButton;
                    this.errorFlag = false;
                    this.$emit('checkCity', true);
                    }
                    catch{
                        this.errorFlag = true;
                        this.flagButton= !this.flagButton;
                    }
                                     
            },
            getWeekDayName(a){
                const day = parseInt(a);
                switch (day) {
                    case 1:
                        return "Понедельник";
                    case 2:
                         return "Вторник";
                    case 3:
                        return "Среда";
                    case 4:
                         return "Четверг";
                    case 5:
                         return "Пятница";
                    case 6:
                         return "Суббота";
                    case 0:
                         return "Воскресенье";
                    }
            }                   
        }
     }      
</script>

<style lang="sass" scoped>
.btn-success
    font-weight: 500
    border: 2px solid #e8e8e8
.catalog__wrapper
    display: flex
    list-style-type: none
    justify-content: space-between
    padding-right: 10px
    padding-left: 10px
.catalog-item
    background-color: white
    min-width: 330px
    min-height: 300px
    border: 6px solid #e8e8e8
    padding: 0px 20px 0px 24px
    margin-top: 110px
    margin-left: 15px
    display: flex
    justify-content: center
    &__img
        height: 80px
        display: block
        margin: 0 auto
        padding-top: 0px
    &__subtitle
        margin-top: 5px
        font-weight: 700
        font-size: 16px
        color: #0d0d0d
        text-align: center
    &__descr
        margin-top: 16px
        font-size: 14px
        font-weight: 600
        margin-bottom: 10px
        color: #0d0d0d
        text-align: center
    hr
        background-color: #665f5f
        margin-top: 19px
        margin-bottom: 21px
        border-bottom: none
    &__wrapper
        display: flex
    &__content
        padding-top: 2px
        padding-right: 5px
    &__list
        width: 100%
        margin-left: 20px
        margin-top: 15px
        padding-right: 2px
        transition: 0.5s all
        padding-left: 0
        margin-bottom: 0
        position: relative
        li
            position: relative
            color: #555555
            font-size: 14px
            font-weight: 500
            margin-bottom: 20px
.error__block
    margin-top: 30px
    text-align: center
    font-weight: 600
</style>