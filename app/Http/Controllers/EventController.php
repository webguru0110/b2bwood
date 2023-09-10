<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTranslation;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Attribute;
use App\Http\Requests\EventRequest;
use App\Traits\AttributeTrait;
use Auth;

class EventController extends Controller
{
    use AttributeTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $events = Event::orderBy('created_at', 'desc');
        $events = $events
                    ->where('title', 'like', '%'.$request->search.'%')
                    ->orwhere('description', 'like', '%'.$request->search.'%');
        $sort_search = $request->search;
        $events = $events->paginate(15);
        return view('backend.events.index', compact('events', 'sort_search', 'approved'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.events.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $event= new Event;
        $event->title = $request->title;
        $event->description = $request->description;
        $event->user_id = auth()->user()->id;
        $event->upload_id = $request->image;
        if($event->save()){
            // Event Translations
            $event_translation = EventTranslation::firstOrNew(['lang' => config('app.locale'), 'event_id' => $event->id]);
            $event_translation->title = $request->title;
            $event_translation->description = $request->description;
            $event_translation->save();

            flash(translate('New Event was created successfully.'))->success();
            if(auth()->user()->isAdmin() || auth()->user()->isStaff()){
                return redirect()->route('admin.events.index');
            }
            return redirect()->route('seller.events');
        }else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, $slug)
    {
        $detailedEvent  = $event->where('slug', $slug)->firstOrFail();
        $content_type = 'App\Models\Event';
        $attributes = Attribute::where('content_type', $content_type)->orderBy('created_at', 'desc')->get();

        // Seo integration with Schema.org
        if(get_setting('enable_seo_event') == "on") {
            seo()->addSchema($detailedEvent->get_schema());
        }

        if ($detailedEvent != null) {
            return view('frontend.events.index', compact('detailedEvent', 'attributes'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $event= Event::findOrFail($id);
        $lang = $request->lang;
        if(!$lang){
            $lang = config('app.locale');
        }
        $content_type = 'App\Models\Event';
        $attributes = Attribute::where('content_type', $content_type)->orderBy('created_at', 'desc')->get();

        return view('backend.events.edit', compact(['event', 'attributes','lang']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Responses
     */
    public function update(EventRequest $request, $id)
    {
        $event= Event::findOrFail($id);
        // Event Attribute Update
        $updated_attributes = $request->except(['_method', '_token', 'title', 'description', 'user_id', 'upload_id']);

        $this->updateAttributes($updated_attributes, $event, "App\Models\Event", $id );

        if($request->lang == config('app.locale')){
            $event->title = $request->title;
            $event->description = $request->description;
        }
        $event->user_id = auth()->user()->id;
        $event->upload_id = $request->image;

        if($event->save()){
            // Event Translations
            $event_translation = EventTranslation::firstOrNew(['lang' => $request->lang, 'event_id' => $event->id]);
            $event_translation->title = $request->title;
            $event_translation->description = $request->description;
            $event_translation->save();

            flash(translate('Event has been updated successfully'))->success();
            if(auth()->user()->isAdmin() || auth()->user()->isStaff()){
                return redirect()->route('admin.events.index');
            }
            else{
                return redirect()->route('seller.events');
            }
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        foreach ($event->event_translations as $key => $event_translation) {
            $event_translation->delete();
        }
        Event::destroy($id);

        flash(translate('Event has been deleted successfully'))->success();
        if(auth()->user()->isAdmin() || auth()->user()->isStaff()){
            return redirect()->route('admin.events.index');
        }
        else{
            return redirect()->route('seller.events');
        }
    }

    /**
     * Display Seller Event
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function seller_events(Request $request)
    {
        $events = Event::with('upload')->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.seller.events.index', compact('events'));
    }

    /**
     * Display Seller Event Create page
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function seller_event_create(Request $request)
    {
        return view('frontend.user.seller.events.event_upload');
    }

    /**
     * Display Seller Event Edit page
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function seller_event_edit(Request $request, $id)
    {
        $event= Event::findOrFail($id);
        $lang = $request->lang;
        if(!$lang){
            $lang = config('app.locale');
        }
        $content_type = 'App\Models\Event';
        $attributes = Attribute::where('content_type', $content_type)->orderBy('created_at', 'desc')->get();

        return view('frontend.user.seller.events.event_edit', compact(['event', 'attributes','lang']));
    }

    /**
     * Display Home Event page
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function all_events(Request $request, $category_id = null)
    {
        $title = '';
        if ($category_id != null) {
            $category = Category::find($category_id);
            $shops = $category->companies();
            $shops = $shops->whereIn('user_id', verified_sellers_id());
            $title = $category->name . ' Companies';
        } else {
            $category = Category::all();

            $shops = Shop::whereIn('user_id', verified_sellers_id());
        }

        $attributeIds = array();
        $events = Event::whereIn('user_id', $shops->get()->pluck('user_id')->toArray());
        foreach ($events->get() as $event) {
            $attributeIds = array_unique(array_merge($attributeIds, $event->attributes->pluck('attribute_id')->toArray()), SORT_REGULAR);
        }
        $attributes = Attribute::whereIn('id', $attributeIds)->where('filterable', true)->get();
        $filters = array();
        foreach ($attributes as $attribute) {
            if ($request->has('attribute_' . $attribute['id']) && $request['attribute_' . $attribute['id']] != null) {
                $filters[$attribute['id']] = $request['attribute_' . $attribute['id']];
                switch ($attribute->type) {
                    case "number":
                        $min_val = $request['attribute_' . $attribute['id']][0];
                        $max_val = $request['attribute_' . $attribute['id']][1];
                        if ($min_val != null && $max_val != null) {
                            $events = $events->whereHas('attributes', function ($relation) use ($min_val, $max_val) {
                                $relation->whereHas('attribute_value', function ($value) use ($min_val, $max_val) {
                                    $value->where('values', '>=', $min_val)->where('values', '<=', $max_val);
                                });
                            });
                        }
                        break;
                    case "checkbox":
                        $checked_arr = $request['attribute_' . $attribute['id']];
                        $events = $events->whereHas('attributes', function ($q) use ($checked_arr) {
                            $q->whereIn('attribute_value_id', $checked_arr);
                        });
                        break;
                    case "country":
                        $code = $request['attribute_' . $attribute['id']];
                        $events = $events->whereHas('attributes', function ($relation) use ($code) {
                            $relation->whereHas('attribute_value', function ($value) use ($code) {
                                $value->where('values', $code);
                            });
                        });
                        break;
                    case "date":
                        $arr_date_range = explode(" to ", $request['attribute_' . $attribute['id']]);
                        if (count($arr_date_range) > 0) {
                            $query = "STR_TO_DATE(`values`, '%d-%m-%y') >= STR_TO_DATE(?, '%d-%m-%y') AND STR_TO_DATE(`values`, '%d-%m-%y') <= STR_TO_DATE(?, '%d-%m-%y')";
                            $events = $events->whereHas('attributes', function ($relation) use ($query, $arr_date_range) {
                                $relation->whereHas('attribute_value', function ($value) use ($query, $arr_date_range) {
                                    $value->whereRaw($query, $arr_date_range);
                                });
                            });
                        }
                        break;
                    default:
                        $val_id = $request['attribute_' . $attribute['id']];
                        $events = $events->whereHas('attributes', function ($q) use ($val_id) {
                            $q->where('attribute_value_id', $val_id);
                        });
                }
            }
        }

        $events = $events->whereIn('user_id', $events->get()->pluck('user_id')->toArray())->paginate(12);

        return view('frontend.event_listing', compact('events', 'attributes', 'category_id', 'filters', 'title'));
    }

    /**
     * Display Home Event page by filtering Category
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function listingByCategory(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->all_events($request, $category->id);
        }
        abort(404);
    }

}
