package com.example.retrofittest;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.GridView;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class MainActivity extends AppCompatActivity {

    //Define StringBuilder to parse JSON data, and two separate arraylists to iterate through
    //Station and Route entries
    private TextView textViewResult;
    private ArrayList<Stations> data;
    private ArrayList<Routes> routeData;
    StringBuilder builder = new StringBuilder();
    List<RouteList> rAllItems = new ArrayList<>();


    //Combine data entries for station and bus routes
    static class RouteList {
        String allRoutes;
        String allBuses;

        public RouteList(String allRoutes, String allBuses) {
            this.allBuses = allBuses;
            this.allRoutes = allRoutes;
        }
    }

    //Store views so findViewById() isn't repetitively called
    static class ViewHolder {
        TextView routeName;
        TextView routeDescription;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        //SetUp GridView and set it to fill screen
        GridView routeGrid = new GridView(this);
        setContentView(R.layout.activity_main);
        routeGrid.setNumColumns(2);
        routeGrid.setColumnWidth(60);
        routeGrid.setVerticalSpacing(20);
        routeGrid.setHorizontalSpacing(20);

        //Configure GSON settings to convert JSON strings to Java objects
        Gson gson = new GsonBuilder()
                .setLenient()
                .create();

        //Configure Retrofit, references JSONResponse class
        //No need for explicit definitions, variables names set up to match those in data
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("https://myttc.ca/")
                .addConverterFactory(GsonConverterFactory.create(gson))
                .build();
        Api api = retrofit.create(Api.class);

        //Retrofit references API for GET() to specific URL
        Call<JSONResponse> call1 = api.getFinch();


        //Add call information to queue and wait for async response from server
        //Upon it, populate View using JSON data via ArrayAdapter
        call1.enqueue(new Callback<JSONResponse>() {
            @Override
            public void onResponse(Call<JSONResponse> call, Response<JSONResponse> response) {
                if (!response.isSuccessful()) {
                    return;
                }
                JSONResponse jsonData = response.body();

                //Store all stations as an array
                data = new ArrayList<>(Arrays.asList(jsonData.getStations()));
                Stations[] temp = jsonData.getStations();

                //Each station contains an array of Routes objects, require a nested for loop
                for (int i = 0; i < data.size(); i++) {
                    builder.append(data.get(i).getName() + "\n");
                    routeData = new ArrayList<>(Arrays.asList(temp[i].getRoutes()));
                    appendRoutes(builder, routeData, data.get(i).getName());
                }

                //Define ArrayAdapter to convert response objects to views
                ArrayAdapter<RouteList> routeAdapter = new ArrayAdapter<RouteList>(MainActivity.this, 0, rAllItems) {
                    //Redefeine getView to be account for RouteList objects
                    //convertView used to recycle views that move out of scrollspace

                    @Override
                    public View getView(int position, View convertView, ViewGroup parent) {
                        RouteList currentRouteTuple = rAllItems.get(position);
                        if (convertView == null) {
                            convertView = getLayoutInflater().inflate(R.layout.custom_item, null, false);
                            //ViewHolder used to contain TextViews in tuples
                            ViewHolder viewHolder = new ViewHolder();
                            //inflate once and store
                            viewHolder.routeName = (TextView) convertView.findViewById(R.id.route_name);
                            viewHolder.routeDescription = (TextView) convertView.findViewById(R.id.route_description);
                            convertView.setTag(viewHolder);
                            //Reference description as it's larger, easier to click
                            //Setup onClickListener, easily retrieve current position/item clicked
                            viewHolder.routeDescription.setOnClickListener(new View.OnClickListener() {
                                @Override
                                public void onClick(View v) {
                                    //Setup implicit intent using Google Maps search query
                                    //Clicking on retrieved view opens it automatically, with station name as search
                                    Uri mapsIntentUri = Uri.parse("geo:0,0?q=" + currentRouteTuple.allRoutes);
                                    Intent mapIntent = new Intent(Intent.ACTION_VIEW, mapsIntentUri);
                                    mapIntent.setPackage("com.google.android.apps.maps");
                                    //Send implicit intent
                                    startActivity(mapIntent);
                                    Log.w("toast", "clicked on " + position);

                                }
                            });
                        }


                        //Ref ViewHolder when required to update TextView, reduce resource usage
                        TextView routeText = ((ViewHolder) convertView.getTag()).routeName;
                        TextView listOfRoutes = ((ViewHolder) convertView.getTag()).routeDescription;

                        routeText.setText(currentRouteTuple.allRoutes);
                        listOfRoutes.setText(currentRouteTuple.allBuses);


                        return convertView;
                    }
                };
                setContentView(routeGrid);
                routeGrid.setAdapter(routeAdapter);
                Log.w("Output", builder.toString());
            }

            @Override
            public void onFailure(Call<JSONResponse> call, Throwable t) {
                textViewResult.setText(t.getMessage());
            }
        });

    }

    //Create RouteList objects and parse text received from GET() call
    public void appendRoutes(StringBuilder b, ArrayList<Routes> addRoute, String currentRoute) {
        //Iterate through RouteList items contained in each ith Stations array entry
        b.delete(0, b.length());
        for (int i = 0; i < addRoute.size(); i++) {
            b.append("Route number " + i + " : " + addRoute.get(i).getRoutename() + "\n");
        }
        String temp = currentRoute + "\n\n" + b.toString() + "\n";
        rAllItems.add(new RouteList(currentRoute, temp));
        Log.w("Strings", temp);
    }

}