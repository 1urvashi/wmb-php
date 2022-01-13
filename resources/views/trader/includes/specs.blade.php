<div class="specifications">
    <h3 class="title">{{trans('frontend.specify')}}</h3>

    <div class="specifications-info">
        
        @foreach($attributeSet as $set)
      
        @if(isset($data[$set->slug]))   
        <h4 class="specifications-info__title">{{$set->name}}</h4>
        @endif
        @if($set->slug == 'test')
        <div class="specifications-info__list">
            <div class="specifications-info__item">
                <span class="specifications-info__item__title">Brand</span>
                <span class="specifications-info__item__info">{{$make}}</span>
            </div>
            <div class="specifications-info__item">
                <span class="specifications-info__item__title">Model</span>
                <span class="specifications-info__item__info">{{$model}}</span>
            </div>
           
            @if(isset($data[$set->slug]))
            @foreach($data[$set->slug] as $attrvalue)
                @if (!empty($attrvalue->attribute_value))
                <div class="specifications-info__item">
                    <span class="specifications-info__item__title">{{$attrvalue->attribute->name}}</span>
                    <span class="specifications-info__item__info">{{$attrvalue->attribute_value}}</span>
                </div>
                @endif
            @endforeach
             @endif
            
        </div>
        @endif
        @endforeach
    </div>
</div>