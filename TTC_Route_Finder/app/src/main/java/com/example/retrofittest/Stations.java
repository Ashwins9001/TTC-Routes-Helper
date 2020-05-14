package com.example.retrofittest;

//Class used to collect stations
//Routes are embedded inside of an array within it, ref it to another class
public class Stations {
    private String name;
    private Routes[] routes;

    public Stations(String name) {
        this.name = name;
    }

    public String getName() {
        return name;
    }

    public Routes[] getRoutes() {
        return routes;
    }
}