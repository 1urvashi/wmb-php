@extends('trader.layouts.master',['title'=>trans('frontend.notify_title'),'class'=>'innerpage'])
@section('content')
        <section id="notifications">
            <div class="container">
                <div class="row content">
                  <form action="">
                    <div class="row">
                        <div class="col-md-6 select pref_wrap" >
                            <h4>MAKE</h4>
                            <select class="js-example-basic-multiple" multiple="multiple">
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                                <option value="AL">Alabama</option>
                                <option value="WY">Wyoming</option>
                            </select>
                        </div>
                        <div class="col-md-6 pref_wrap">
                            <h4>YEAR</h4>
                            <div data-role="main" class="ui-content">
                                  <div class="sliders" id="sliderab"></div> 
                               <!--  For reference- https://refreshless.com/nouislider/ -->
                              </div>
                        </div>
                    </div>
                    <div class="row second">

                    </div>
                      <div class="row">
                        <div class="col-md-12">
                             <button type="submit" id="pref_sub">Submit</button>

                        </div>

                      </div>
                  </form>

                </div>
            </div>
        </section>
@endsection

@push('scripts')
  @endpush
