package com.example.retrofittest;

import retrofit2.Call;
import retrofit2.http.GET;

//API to define client requests
public interface Api {

    @GET("/finch_station.json")
    Call<JSONResponse> getFinch();

    @GET("/union_station.json")
    Call<JSONResponse> getUnion();

    @GET("/spadina_station.json")
    Call<JSONResponse> getSpadina();


}
