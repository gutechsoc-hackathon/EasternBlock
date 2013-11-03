package com.guts.hackathon;

/**
 * Created by Gabrielius on 13.11.3.
 */
public class TagPOJO {

    private String name;
    private long count;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public long getCount() {
        return count;
    }

    public void setCount(long count) {
        this.count = count;
    }

    public String toString() {
        return getName();
    }
}
