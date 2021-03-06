@if($date->getShortName() == 'rehearsal' && !$date->isAttending(Auth::user()))
    <?php $notAttending = true; ?>
@else
    <?php $notAttending = false; ?>
@endif

@if($date->getShortName() == 'gig')
    <?php $attending = $date->isAttending(Auth::user()); ?>
@else
    <?php $attending = false; ?>
@endif

@if(true === $date->needs_answer)
    <?php $unanswered = true !== $date->hasAnswered(Auth::user()); ?>
@else
    <?php $unanswered = false; /* event doesnt need answer (like birthday) */ ?>
@endif

<div class="row list-item">
    <div class="col-xs-12 context-box-2d event event-{{ $date->getShortName() }} {{ $notAttending || $attending == 'no' ? 'event-not-going' : '' }} {{ $unanswered ? 'event-unanswered' : '' }}">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-10">
                <h4 class="title">
                    {{ $date->getTitle() }}
                    <span class="not-going-note" style="display: <?php echo $notAttending ? 'inline' : 'none'; ?>;">{{  ' &ndash; ' . trans('date.not_attending') }}</span>
                    @if($date->hasPlace())
                        <br>
                        {{ $date->place }}
                        <a href="https://www.google.com/maps/search/{{ $date->place }}/" title="{{ trans('date.address_search') }}" target="_blank" class="pull-right text-large">{{ trans('date.goto_maps') }} <i class="fa fa-map-o"></i></a>
                    @endif
                </h4>

                <p class="date">
                    @if($date->isAllDay())
                        {{ $date->getStart()->formatLocalized('%A, den %d.%m.%Y') }}
                    @else
                        @if(0 === $date->getEnd()->diffInDays($date->getStart()))
                            {{ $date->getStart()->formatLocalized('%A, den %d.%m.%Y') }}
                            <br>
                            {{ $date->getStart()->formatLocalized('%H:%M') . ' - ' . $date->getEnd()->formatLocalized('%H:%M') }}
                        @else
                            {{ $date->getStart()->formatLocalized('%A, den %d.%m.%Y %H:%M') }}
                            <br>
                            bis&nbsp;{{ $date->getEnd()->formatLocalized('%A, den %d.%m.%Y %H:%M') }}
                        @endif
                    @endif
                </p>
                <span class="date_descr">
                    {{ $date->description }}
                </span>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-2 event-controls">
                @if($date->getShortName() == 'rehearsal')
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="slider-2d" data-function="changeAttendance" data-excuse-url="{{ route('attendance.excuseSelf', ['rehearsal_id' => $date->getId()]) }}" data-attend-url="{{ route('attendance.confirmSelf', ['rehearsal_id' => $date->getId()]) }}">
                                <input type="checkbox"<?php echo $notAttending ? '' : ' checked="checked"'; ?> id="slider-attending-{{ $date->getShortName() }}-{{ $date->getId() }}">
                                <label for="slider-attending-{{ $date->getShortName() }}-{{ $date->getId() }}">
                                    <span class="slider"></span>
                                    <i class="fa fa-calendar-times-o" title="{{ trans('date.excuse') }}"></i>
                                    <i class="fa fa-calendar-check-o" title="{{ trans('date.attend') }}"></i>
                                </label>
                            </span>
                        </div>
                    </div>
                @elseif($date->getShortName() == 'gig')
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="button-set-2d">
                                <a href="#" class="btn btn-2d btn-no{{ $attending == 'no' ? ' btn-pressed' : '' }}" data-url="{{ route('commitment.commitSelf', ['gig_id' => $date->getId()]) }}" data-attendance="no">
                                    <i class="fa fa-calendar-times-o"></i>
                                </a>
                                <a href="#" class="btn btn-2d btn-maybe{{ $attending == 'maybe' ? ' btn-pressed' : '' }}" data-url="{{ route('commitment.commitSelf', ['gig_id' => $date->getId()]) }}" data-attendance="maybe">
                                    <i class="fa fa-calendar-o fa-with-overlay">?</i>
                                </a>
                                <a href="#" class="btn btn-2d btn-yes{{ $attending == 'yes' ? ' btn-pressed' : '' }}" data-url="{{ route('commitment.commitSelf', ['gig_id' => $date->getId()]) }}" data-attendance="yes">
                                    <i class="fa fa-calendar-check-o"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                @endif
                @if(isset($date->getEventOptions()['url']) && Auth::user()->isAdmin($date->getShortName()))
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="edit-btn-container">
                                <a href="{{ $date->getEventOptions()['url'] }}" class="btn btn-2d" title="{{ trans('form.edit') }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>