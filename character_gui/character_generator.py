import dearpygui.dearpygui as dpg
import json
from copy import deepcopy

import glob
import os
import time


class CharacterGenerator:
    def __init__(self, character, create_mode) -> None:
        self._imported_character = character

        self._current_character = deepcopy(self._imported_character)

        self._text_input_width = 200

        self._platoon_alternatives = self._current_character["Config"]["platoons"]
        self._speciality_alternatives = self._current_character["Config"][
            "specialities"
        ]
        self._gender_alternatives = self._current_character["Config"]["genders"]

        self._create_mode = create_mode
        self._section_title_color = [150, 250, 150]

        self._item_refs = {
            "attributes": dict(),
            "traits": dict(),
            "advantages": dict(),
            "disadvantages": dict(),
            "skills": dict(),
            "stats": dict(),
            "specialization": dict(),
        }

        self._stats = {
            "Carry Capacity": 55,
            "Combat Load": 25,
            "Psycho Limit": 4,
            "Fear Limit": 4,
            "Stunt Cap": 2,
            "Attribute Points": self._get_base_attribute_points()
            - self._get_total_attribute_points(),
            "Experience Points": self._get_base_experience_points()
            - self._get_total_xp_usage(),
        }

    def _get_base_attribute_points(self) -> int:
        return self._imported_character["Config"]["Starting AP"]

    def _get_base_experience_points(self) -> int:
        return self._imported_character["Config"]["Starting EP"]

    @staticmethod
    def _get_total_knowledge_cost(
        skills_or_specializations: dict, default_cost: list
    ) -> int:
        """
        Calulate the xp cost for either all skills or all specializations.
        """
        sum_cost = 0
        for category in skills_or_specializations.values():
            for knowledge in category.values():
                if "cost_table" in knowledge:
                    cost_table = knowledge["cost_table"]
                else:
                    cost_table = default_cost
                sum_cost = sum_cost + cost_table[knowledge["value"]]
        return sum_cost

    def _get_total_attribute_points(self) -> int:
        sum_points = 0
        for attribute in self._current_character["Attributes"].values():
            sum_points = sum_points + attribute["value"]
        return sum_points

    def _get_total_xp_usage(self) -> int:
        return (
            self._get_total_knowledge_cost(
                skills_or_specializations=self._current_character["Skills"],
                default_cost=self._imported_character["Config"]["skill_cost_table"],
            )
            + self._get_total_knowledge_cost(
                skills_or_specializations=self._current_character["Specializations"],
                default_cost=self._imported_character["Config"][
                    "specialization_cost_table"
                ],
            )
            + self._get_total_property_cost(self._current_character["Advantages"])
            + self._get_total_property_cost(self._current_character["Disadvantages"])
        )

    @staticmethod
    def _get_total_property_cost(properties: dict) -> int:
        """
        Calculate the total cost from boolean poperties .
        For example 'Advantages'.
        """
        sum_cost = 0
        for property in properties.values():
            if property["value"]:
                sum_cost = sum_cost + property["cost"]
        return sum_cost

    def _get_allowed_min_value(self, item: dict) -> int:
        min = item["value"]
        if self._create_mode:
            min = item["min"]
        return min

    def _is_check_box_change_allowed(self, item: dict) -> bool:
        """
        When editing an existing character, boxes are not allowed to be unchecked.
        """
        allowed = False
        if self._create_mode or not item["value"]:
            allowed = True
        return allowed

    def _attribute_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any slider for attribute points have changed.
        """
        self._set_value_and_display_difference(
            property="Attributes",
            label=user_data["label"],
            sender=sender,
            new_value=app_data,
        )
        self._update_ap_status()

    def _get_value_from_character_state(
        self, state: dict, property: str, label: str, category: str = None
    ) -> int:
        """
        Helper function to get a specific value.
        """
        if category:
            return state[property][category][label]["value"]
        else:
            return state[property][label]["value"]

    def _set_value_in_character_state(
        self, state: dict, property: str, label: str, value: int, category: str = None
    ):
        """
        Helper function to set a specific value.
        """
        if category:
            state[property][category][label]["value"] = value
        else:
            state[property][label]["value"] = value

    def _set_value_and_display_difference(
        self,
        property: str,
        label: str,
        sender: int,
        new_value: int,
        category: str = None,
    ):
        """
        Helper function that will set the associated value from the slider
        and display the difference compared to the previous saved state.
        """
        self._set_value_in_character_state(
            state=self._current_character,
            property=property,
            category=category,
            label=label,
            value=new_value,
        )
        difference = new_value - self._get_value_from_character_state(
            state=self._imported_character,
            property=property,
            category=category,
            label=label,
        )

        if difference != 0:
            difference_string = str(difference)
            if difference > 0:
                difference_string = "+" + difference_string

            dpg.set_item_label(item=sender, label=difference_string)
        else:
            dpg.set_item_label(item=sender, label="")

    def _skills_specializations_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any slider for skill/specialization points have changed.
        """
        self._set_value_and_display_difference(
            property=user_data["property"],
            category=user_data["category"],
            label=user_data["label"],
            sender=sender,
            new_value=app_data,
        )
        self._update_xp_status()

    def _property_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any checkbox for advantages points have changed.
        """
        self._set_value_in_character_state(
            state=self._current_character,
            property=user_data["property"],
            label=user_data["label"],
            value=app_data,
        )
        self._update_xp_status()

    def _trait_callback(self, sender, app_data, user_data):
        """
        Triggered when any checkbox for advantages points have changed.
        """
        self._current_character["Traits"]["value"] = app_data

    def _update_ap_status(self):
        """
        Update the printout of available attribute points.
        Must be called whenever a related value have been change.
        """
        remaining = (
            self._get_base_attribute_points() - self._get_total_attribute_points()
        )
        dpg.set_value(
            item=self._item_refs["stats"]["Attribute Points"], value=remaining
        )

    def _update_xp_status(self):
        """
        Update the printout of available experience points.
        Must be called whenever a related value have been change.
        """
        remaining = self._get_base_experience_points() - self._get_total_xp_usage()
        dpg.set_value(
            item=self._item_refs["stats"]["Experience Points"], value=remaining
        )

    @staticmethod
    def _split_dict(source, num_per_part):
        """
        Helper function to dived a set of components into groups
        in order to control the number of items shown horisontally.
        For example.
        * Limit the amount traits/advantages/disadvantages for each column.
        " Limit the amount of skill/specialization sub categories for each column.
        """
        split_items = []
        part = dict()
        row_count = 0
        for item_key, item_value in source.items():
            part[item_key] = item_value
            row_count = row_count + 1
            if row_count >= num_per_part:
                split_items.append(part)
                part = dict()
                row_count = 0
        if row_count > 0:
            split_items.append(part)
        return split_items

    def _add_attributes_tab(self, attributes: dict, callback=None) -> dict:
        dpg.add_text("Attributes")
        with dpg.table(
            header_row=False,
            row_background=False,
            no_host_extendX=True,
        ):
            dpg.add_table_column(width_fixed=True, init_width_or_weight=130)
            dpg.add_table_column(width_fixed=True, init_width_or_weight=100)
            item_refs = dict()
            for attribute_key, attribute_values in attributes.items():
                with dpg.table_row():
                    dpg.add_text(f"{attribute_key}:")
                    item_id = dpg.add_slider_int(
                        default_value=attribute_values["value"],
                        min_value=self._get_allowed_min_value(attribute_values),
                        max_value=attribute_values["max"],
                        width=50,
                        user_data={"label": attribute_key},
                        callback=callback,
                    )
                    item_refs[attribute_key] = item_id
        return item_refs

    def _add_character_setup(self):
        dpg.add_text("Character Setup", color=self._section_title_color)
        with dpg.table(
            header_row=False, policy=dpg.mvTable_SizingStretchProp, row_background=False
        ):
            dpg.add_table_column()
            dpg.add_table_column()
            with dpg.table_row():
                dpg.add_text("Player:")
                if self._create_mode:
                    dpg.add_input_text(
                        tag="player_input_text",
                        default_value=self._current_character["Player"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(self._current_character["Player"])

            with dpg.table_row():
                dpg.add_text("E-mail:")
                if self._create_mode:
                    dpg.add_input_text(
                        tag="email_input_text",
                        default_value=self._current_character["E-mail"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(self._current_character["E-mail"])

            with dpg.table_row():
                dpg.add_text("Name:")
                if self._create_mode:
                    dpg.add_input_text(
                        default_value=self._current_character["Name"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(self._current_character["Name"])

            with dpg.table_row():
                dpg.add_text("Platoon:")
                current_platoon = self._platoon_alternatives[
                    self._current_character["Platoon"]
                ]
                if self._create_mode:
                    dpg.add_combo(
                        items=self._platoon_alternatives,
                        width=self._text_input_width,
                        default_value=current_platoon,
                    )
                else:
                    dpg.add_text(current_platoon)

            with dpg.table_row():
                dpg.add_text("Rank:")
                dpg.add_text(self._current_character["Rank"])

            with dpg.table_row():
                dpg.add_text("Speciality:")
                current_speciality = self._speciality_alternatives[
                    self._current_character["Speciality"]
                ]
                if self._create_mode:
                    dpg.add_combo(
                        items=self._speciality_alternatives,
                        width=self._text_input_width,
                        default_value=current_speciality,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(current_speciality)

            with dpg.table_row():
                dpg.add_text("Gender:")
                current_gender = self._gender_alternatives[
                    self._current_character["Gender"]
                ]
                if self._create_mode:
                    dpg.add_combo(
                        tag="gender_input_combo",
                        items=self._gender_alternatives,
                        width=self._text_input_width,
                        default_value=current_gender,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(current_gender)

            with dpg.table_row():
                dpg.add_text("Age:")
                current_age = self._current_character["Age"]

                if self._create_mode:
                    dpg.add_slider_int(
                        tag="age_input_combo",
                        min_value=self._imported_character["Config"]["age"]["min"],
                        max_value=self._imported_character["Config"]["age"]["max"],
                        default_value=current_age,
                        enabled=self._create_mode,
                    )
                else:
                    dpg.add_text(current_age)

    def _add_property_check_boxes(
        self,
        character,
        property,
        num_per_row=4,
        label_width=130,
        cost_width=25,
        show_cost=True,
        callback=None,
    ):
        """
        Add components for traits, advantages or disadvantages.
        """
        item_refs = dict()
        properties = character[property]
        split_items = self._split_dict(properties, num_per_row)

        with dpg.group(horizontal=True):
            for part in split_items:
                with dpg.group():
                    for property_key, propery_value in part.items():
                        with dpg.group(horizontal=True):
                            with dpg.table(
                                header_row=False,
                                row_background=False,
                                no_host_extendX=True,
                            ):
                                dpg.add_table_column(
                                    width_fixed=True, init_width_or_weight=20
                                )
                                dpg.add_table_column(
                                    width_fixed=True, init_width_or_weight=label_width
                                )
                                if show_cost:
                                    dpg.add_table_column(
                                        width_fixed=True,
                                        init_width_or_weight=cost_width,
                                    )
                                    cost = propery_value["cost"]
                                else:
                                    cost = None
                                with dpg.table_row():
                                    item_id = dpg.add_checkbox(
                                        user_data={
                                            "property": property,
                                            "label": property_key,
                                        },
                                        callback=callback,
                                        default_value=propery_value["value"],
                                        enabled=self._is_check_box_change_allowed(
                                            propery_value
                                        ),
                                    )
                                    item_refs[property_key] = item_id
                                    dpg.add_text(property_key)
                                    if show_cost:
                                        dpg.add_text(f"({propery_value['cost']})")
        return item_refs

    def _add_skill_input(
        self, character, property, num_per_row=3, label_width=170, callback=None
    ):
        """
        Add sliders for skills.
        """
        skill_categories = character[property]
        item_refs = dict()
        split_items = self._split_dict(skill_categories, num_per_row)
        with dpg.group(horizontal=True):
            for part in split_items:
                with dpg.group():
                    for category_key, category_value in part.items():
                        with dpg.group(width=300):
                            dpg.add_text(category_key, color=self._section_title_color)
                            for skill_key, skill_value in category_value.items():
                                with dpg.table(
                                    header_row=False,
                                    row_background=False,
                                    no_host_extendX=True,
                                ):
                                    dpg.add_table_column(
                                        width_fixed=True,
                                        init_width_or_weight=label_width,
                                    )
                                    dpg.add_table_column(
                                        width_fixed=True, init_width_or_weight=100
                                    )
                                    with dpg.table_row():
                                        dpg.add_text(skill_key, indent=5)
                                        item_id = dpg.add_slider_int(
                                            default_value=skill_value["value"],
                                            min_value=self._get_allowed_min_value(
                                                skill_value
                                            ),
                                            max_value=skill_value["max"],
                                            width=50,
                                            user_data={
                                                "property": property,
                                                "category": category_key,
                                                "label": skill_key,
                                            },
                                            callback=callback,
                                        )
                                        item_refs[skill_key] = item_id
        return item_refs

    def main(self):
        with dpg.window(
            width=300,
            height=800,
            pos=[0, 0],
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            with dpg.group(width=300):
                self._add_character_setup()

                # Display Stats
                dpg.add_text("Stats", color=self._section_title_color)
                with dpg.table(
                    header_row=False,
                    policy=dpg.mvTable_SizingFixedFit,
                    row_background=False,
                ):
                    dpg.add_table_column()
                    dpg.add_table_column()
                    for stat_label, stat_value in self._stats.items():
                        with dpg.table_row():
                            dpg.add_text(stat_label)
                            self._item_refs["stats"][stat_label] = dpg.add_text(
                                stat_value
                            )
            with dpg.group(width=300):
                dpg.add_spacer(height=50)
                if self._create_mode:
                    dpg.add_button(label="Submit new character to Skynet")
                else:
                    dpg.add_button(label="Submit update to Skynet")

        with dpg.window(
            width=800,
            height=800,
            pos=[301, 0],
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            with dpg.group(width=300):
                with dpg.tab_bar(tag="Tabs"):
                    with dpg.tab(label="Attributes"):
                        # Display attributes
                        self._item_refs["Attributes"] = self._add_attributes_tab(
                            self._current_character["Attributes"],
                            callback=self._attribute_callback,
                        )

                    with dpg.tab(label="Skills"):
                        self._item_refs["Skills"] = self._add_skill_input(
                            character=self._current_character,
                            property="Skills",
                            callback=self._skills_specializations_callback,
                        )
                    with dpg.tab(label="Specializations"):
                        self._item_refs["Specializations"] = self._add_skill_input(
                            character=self._current_character,
                            property="Specializations",
                            num_per_row=2,
                            callback=self._skills_specializations_callback,
                        )

                    with dpg.tab(label="Traits:"):
                        self._item_refs["Traits"] = self._add_property_check_boxes(
                            character=self._current_character,
                            property="Traits",
                            show_cost=False,
                            callback=self._trait_callback,
                        )

                    with dpg.tab(label="Advantages:"):
                        self._item_refs["Advantages"] = self._add_property_check_boxes(
                            character=self._current_character,
                            property="Advantages",
                            callback=self._property_callback,
                        )

                    with dpg.tab(label="Disadvantages:"):
                        self._item_refs[
                            "Disadvantages"
                        ] = self._add_property_check_boxes(
                            character=self._current_character,
                            property="Disadvantages",
                            cost_width=30,
                            callback=self._property_callback,
                        )

        self._update_xp_status()


class CharacterSelector:
    """
    Allow the user to either create a new character or load an existing one.
    """

    def __init__(self):
        self._section_title_color = [150, 250, 150]

        self._available_characters = []

        self._create_mode = False

        self._characters_files = glob.glob("local_characters/*.json")
        for file in self._characters_files:
            path, file = os.path.split(file)
            character, ext = file.split(".")
            self._available_characters.append(character.replace("_", " ").title())

        self._selected_character = self._available_characters[0]
        self._selected_character_file = self._characters_files[0]

    def _character_list_callback(self, sender, app_data):
        """
        Called when selecting a character in the list.
        """
        self._selected_character = app_data

    def _edit_button_callback(self, sender, app_data):
        """
        Continue with the selected character and setup next stage for edit mode.
        """
        self._create_mode = False
        self._selected_character_file = self._characters_files[
            self._available_characters.index(self._selected_character)
        ]

        ci = CharacterImport.from_json(self._selected_character_file)
        cg = CharacterGenerator(character=ci.get_character(), create_mode=False)
        cg.main()

    def _create_button_callback(self, sender, app_data):
        """
        Continue with template character and setup next stage for creation mode.
        """
        self._selected_character_file = r"local_characters/template/template.json"

        ci = CharacterImport.from_json(self._selected_character_file)
        cg = CharacterGenerator(character=ci.get_character(), create_mode=True)
        cg.main()

    def _connect_button_callback(self, sender, app_data):
        self._add_character_selection()

    def _add_login(self):
        with dpg.group(width=200):
            dpg.add_text("Credentials:", color=self._section_title_color)
            with dpg.group(horizontal=True):
                dpg.add_text("Username:", color=self._section_title_color, indent=10)
                dpg.add_input_text(default_value="skynet_user", enabled=False, width=50)
            with dpg.group(horizontal=True):
                dpg.add_text("Password:", color=self._section_title_color, indent=10)
                dpg.add_input_text(default_value="skynet_password", enabled=False)
            dpg.add_button(label="Connect", callback=self._connect_button_callback)
            dpg.add_spacer(height=20)

    def _add_character_selection(self):
        with dpg.group(width=300, parent="character_selector"):
            dpg.add_button(
                label="Create New Character", callback=self._create_button_callback
            )
            dpg.add_spacer(height=20)

            dpg.add_listbox(
                self._available_characters, callback=self._character_list_callback
            )
            dpg.add_button(label="Edit Character", callback=self._edit_button_callback)

    def main(self):
        """
        Set Login options.
        Select existing character or create new
        """
        with dpg.window(
            width=380,
            height=400,
            pos=[0, 0],
            tag="character_selector",
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            self._add_login()


class CharacterImport:
    """
    Handle import of character. Currenty only from json-file.
    """

    def __init__(self, character):
        self._character = character

    @classmethod
    def from_json(cls, character_path):
        with open(character_path) as setup_file:
            imported_character = json.load(setup_file)

        # Convert from json 0/1 to false/true
        for category in ["Traits", "Advantages", "Disadvantages"]:
            for item_key in imported_character[category].keys():
                imported_character[category][item_key]["value"] = bool(
                    imported_character[category][item_key]["value"]
                )
        return cls(imported_character)

    def get_character(self):
        return self._character


if __name__ == "__main__":
    dpg.create_context()
    cs = CharacterSelector()
    cs.main()

    dpg.create_viewport(title="USCM Character Editor", width=1150, height=850)
    dpg.setup_dearpygui()
    dpg.show_viewport()
    dpg.start_dearpygui()
    dpg.destroy_context()
