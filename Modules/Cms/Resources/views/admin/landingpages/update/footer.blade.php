<div class="kt-section kt-section--first">
    <div class="row">
        <div class="col-md-12">
            <div class="kt-wizard-v4__form">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="kt-section__body">
                            <div class="kt-section__content">
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'footer_whatsapp',
                                                'name'              => 'footer_whatsapp',
                                                'type'              => 'number',
                                                'label'             => 'whatsapp',
                                                'placeholder'       => 'whatsapp',
                                                'help'              => '',
                                                'value'             => old('footer_whatsapp',$model->footer_whatsapp),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'footer_facebook',
                                                'name'              => 'footer_facebook',
                                                'type'              => 'text',
                                                'label'             => 'facebook',
                                                'placeholder'       => 'facebook',
                                                'help'              => '',
                                                'value'             => old('footer_facebook',$model->footer_facebook),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'footer_youtube',
                                                'name'              => 'footer_youtube',
                                                'type'              => 'text',
                                                'label'             => 'youtube',
                                                'placeholder'       => 'youtube',
                                                'help'              => '',
                                                'value'             => old('footer_youtube',$model->footer_youtube),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('cms::components.inputs.text', [
                                            'options' => [
                                                'id'                => 'footer_instagram',
                                                'name'              => 'footer_instagram',
                                                'type'              => 'text',
                                                'label'             => 'instagram',
                                                'placeholder'       => 'instagram',
                                                'help'              => '',
                                                'value'             => old('footer_instagram',$model->footer_instagram),
                                                'required'          => false,
                                                'inline'            => false,
                                                'maxlength'         => 191,
                                                'status_removal'    => false
                                            ]
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
